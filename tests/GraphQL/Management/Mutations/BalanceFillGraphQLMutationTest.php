<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class BalanceFillGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input = ['number' => 8600332914249390, 'expiry' => '2509', 'amount' => 1000];

    protected function setUp(): void
    {
        parent::setUp();

        $feature = Feature::findByName(FeatureEnum::ATMOS_CARD_PAY);
        $feature->data = [
            'username' => 'username',
            'password' => 'password',
            'store_id' => '1',
            'expires_in' => 3600,
            'access_token' => 'access_token',
            'token_type' => 'token_type',
        ];
        $feature->saveOrFail();

        AtmosFaker::getToken();
    }

    #[Test]
    public function successBalanceFill()
    {
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorBalanceFill()
    {
        AtmosFaker::createPayment(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull('Ошибка', $response->json('errors.0.message'));
    }

    #[Test]
    public function errorValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['number' => null, 'expiry' => null, 'amount' => null])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$number" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$expiry" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$amount" of non-null type "Int!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(array_merge($this->_input, ['number' => 987678, 'expiry' => 'iuhgg']))
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'number', 'digits' => 16]), $response->json('errors.0.extensions.validation.number.0'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'expiry', 'digits' => 4]), $response->json('errors.0.extensions.validation.expiry.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();

        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::BALANCE_FUND_CREATE)->firstOrFail();

        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull('Unauthorized', $response->json('errors.0.message'));

        $role->permissions()->attach($permission);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));
        $this->assertSuccessResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation BalanceFill($number: BigInteger!, $expiry: String!, $amount: Int!) {
                balanceFill(number: $number, expiry: $expiry, amount: $amount) {
                    transaction_id
                    result_code
                    result_description
                }
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'balanceFill' => [
                    'transaction_id',
                    'result_code',
                    'result_description',
                ],
            ],
        ]);
    }
}
