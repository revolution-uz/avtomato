<?php

namespace Customer\Mutations;

use App\Enums\Company\PhoneTypeEnum;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DetachPhoneGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private array $_phonesInput;

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create();
        Phone::query()->forceDelete();
        foreach (Phone::factory()->count(5)->create() as $phone) {
            $this->_customer->phones()->attach($phone->id, ['type' => PhoneTypeEnum::randomValue()]);
        }
    }

    #[Test]
    public function successDetachPhone()
    {
        $this->_phonesInput = $this->_customer->phones()->inRandomOrder()->limit(3)->pluck('phone')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'phones' => $this->_phonesInput,
                ])
            );
        $this->assertSuccessDetachPhoneResponse($response);
    }

    #[Test]
    public function errorValidations()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => null,
                    'phones' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$phones" of non-null type "[BigInteger]!" must not be null.'));

        // wrong phone number
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'phones' => ['998931234567'],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.exists', ['attribute' => 'phones.0']), $response->json('errors.0.extensions.validation')['phones.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DetachPhone($customer_id: Int!, $phones: [BigInteger]!) {
                detachPhone(customer_id: $customer_id, phones: $phones)
            }
        ';

        return $query;
    }

    private function assertSuccessDetachPhoneResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'detachPhone',
            ],
        ]);
        $this->assertTrue($response->json('data.detachPhone'));
        $this->assertTrue($this->_customer->phones()->whereIn('phone', $this->_phonesInput)->doesntExist());
    }
}
