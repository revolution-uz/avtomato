<?php

namespace Customer\Mutations;

use App\Enums\Company\PhoneTypeEnum;
use App\Models\Company\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AttachPhoneGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private array $_input;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_customer = Customer::factory()->create();

        $this->_input = [
            'customer_id' => $this->_customer->id,
            'phone' => '99893' . rand(1000000, 9999999),
            'type' => (int) PhoneTypeEnum::randomValue(),
            'comment' => null,
        ];
    }

    #[Test]
    public function successAttachWithoutComment()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessAttachPhoneResponse($response);
    }

    #[Test]
    public function successCreateWithComment()
    {
        $this->_input['comment'] = Str::random(64);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessAttachPhoneResponse($response);
    }

    #[Test]
    public function errorPhoneValidation()
    {
        // required error
        $this->_input['phone'] = null;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$phone" of non-null type "BigInteger!" must not be null.', $response->json('errors.0.message'));

        // wrong code
        $this->_input['phone'] = '99810' . rand(1000000, 9999999);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('The phone is invalid phone number.', $response->json('errors.0.extensions.validation.phone.0'));

        // wrong format
        $this->_input['phone'] = '99893' . rand(100000, 999999);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('The phone is invalid phone number.', $response->json('errors.0.extensions.validation.phone.0'));
    }

    #[Test]
    public function errorOtherValidations()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => null,
                    'phone' => null,
                    'type' => null,
                    'comment' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$phone" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$type" of non-null type "Int!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => Customer::query()->orderByDesc('id')->first()->id + 1,
                    'phone' => Str::random(12),
                    'type' => 1000,
                    'comment' => Str::random(65),
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'customer id']), $response->json('errors.0.extensions.validation.customer_id.0'));
        $this->assertEquals(trans('validation.integer', ['attribute' => 'Телефон']), $response->json('errors.0.extensions.validation.phone.0'));
        $this->assertEquals(trans('validation.in', ['attribute' => 'type']), $response->json('errors.0.extensions.validation.type.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'comment', 'max' => 64]), $response->json('errors.0.extensions.validation.comment.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation AttachPhone(
                $customer_id: Int!
                $phone: BigInteger!
                $type: Int!
                $comment: String
            ) {
                attachPhone(
                    customer_id: $customer_id
                    phone: $phone
                    type: $type
                    comment: $comment
                ) {
                    id
                    phone
                    type
                    comment
                    created_at
                    updated_at
                }
            }
        ';

        return $query;
    }

    private function assertSuccessAttachPhoneResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'attachPhone' => [
                    'id',
                    'phone',
                    'type',
                    'comment',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $phone = $this->_customer->phones()->find($response->json('data.attachPhone.id'));
        $this->assertNotNull($phone);
        $this->assertEquals($this->_input['phone'], $phone->phone);
        $this->assertEquals($this->_input['type'], $phone->pivot->type);
        $this->assertEquals($this->_input['comment'], $phone->pivot->comment);

        $this->assertEquals($phone->id, $response->json('data.attachPhone.id'));
        $this->assertEquals($phone->phone, $response->json('data.attachPhone.phone'));
        $this->assertEquals($phone->pivot->type, $response->json('data.attachPhone.type'));
        $this->assertEquals($phone->pivot->comment, $response->json('data.attachPhone.comment'));
        $this->assertEquals($phone->pivot->created_at->timestamp, $response->json('data.attachPhone.created_at'));
        $this->assertEquals($phone->pivot->updated_at->timestamp, $response->json('data.attachPhone.updated_at'));
    }
}
