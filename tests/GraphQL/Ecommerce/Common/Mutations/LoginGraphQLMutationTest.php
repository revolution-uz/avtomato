<?php

namespace Tests\GraphQL\Ecommerce\Common\Mutations;

use App\Models\Company\CardConfirmationSms;
use App\Models\Company\Ecommerce\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class LoginGraphQLMutationTest extends GraphQLTestCase
{
    private array $_headers = [
        'Device-Name' => 'test',
        'Device-OS' => 'android',
        'Device-Type' => 'mobile',
    ];

    private array $_variables = ['phone' => '998901234567', 'code' => '123456'];

    #[Test]
    public function failLoginCases()
    {
        // Necessary headers does not exist
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery($this->_variables)
        );
        $this->assertFailLoginResponse($response);

        // Confirmation does not exist
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery($this->_variables), $this->_headers
        );
        $this->assertFailLoginResponse($response);

        // Confirmation code expired
        CardConfirmationSms::factory()->create(array_merge($this->_variables, ['created_at' => Carbon::now()->subSeconds(2 * 60 + 1)]));
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery($this->_variables), $this->_headers
        );
        $this->assertFailLoginResponse($response);

        // Wrong confirmation code
        CardConfirmationSms::factory()->create(array_merge($this->_variables, ['code' => '012345']));
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery($this->_variables), $this->_headers
        );
        $this->assertFailLoginResponse($response);
    }

    #[Test]
    public function successLoginWithExistingUser()
    {
        User::factory()->create(['phone' => $this->_variables['phone']]);
        CardConfirmationSms::query()->create($this->_variables);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery($this->_variables), $this->_headers
        );

        $this->assertSuccessLoginResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery()
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$phone" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$code" of required type "String!" was not provided.'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['phone' => Str::random(), 'code' => Str::random()])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'Телефон', 'digits' => 12]), $response->json('errors.0.extensions.validation.phone.0'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'code', 'digits' => 6]), $response->json('errors.0.extensions.validation.code.0'));

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['phone' => '998001234567', 'code' => '123456'])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('The phone is invalid phone number.', $response->json('errors.0.extensions.validation.phone.0'));
    }

    public function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation EcommerceLogin($phone: String!, $code: String!) {
                    ecommerceLogin(phone: $phone, code: $code)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessLoginResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['ecommerceLogin']]);
        $this->assertIsString($response->json('data.ecommerceLogin'));

        $this->assertTokenIsAvailable($response->json('data.ecommerceLogin'));
    }

    private function assertFailLoginResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals($response->json('errors.0.message'), 'Phone or code is wrong');
    }

    private function assertTokenIsAvailable(string $token)
    {
        $query = [
            'query' => <<<'GRAPHQL'
                query UserCarts {
                    userCarts {
                        id
                    }
                }
            GRAPHQL,
        ];

        $response = $this->postJson(
            route('graphql.ecommerce-client'), $query, ['Authorization' => "Bearer {$token}"]
        );

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['userCarts']]);
    }
}
