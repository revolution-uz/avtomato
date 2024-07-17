<?php

namespace Customer\Queries;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Attachment;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentComment;
use App\Models\Company\InstallmentGraph;
use App\Models\Company\Phone;
use App\Models\Company\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CustomerByPassportGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private array $_structure = [
        'id',
        'pinfl',
        'tin',
        'passport_series',
        'passport_number',
        'passport_given_date',
        'passport_expiration_date',
        'passport_given_place',
        'passport_citizenship',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'birth_place',
        'gender',
        'created_at',
        'updated_at',
        'deleted_at',
        'cards' => [
            '*' => [
                'id',
                'customer_id',
                'number',
                'card_type',
                'owner',
                'expiry',
                'is_connected',
                'phone',
                'status',
                'balance',
                'created_at',
                'updated_at',
            ],
        ],
        'addresses' => [
            '*' => [
                'id',
                'address',
                'type',
                'region_id',
                'district_id',
                'mahalla_id',
                'created_at',
                'updated_at',
                'deleted_at',
                'region' => [
                    'id',
                    'name',
                ],
                'district' => [
                    'id',
                    'region_id',
                    'name',
                ],
                'mahalla' => [
                    'id',
                    'district_id',
                    'name',
                ],
            ],
        ],
        'phones' => [
            '*' => [
                'id',
                'phone',
                'type',
                'comment',
                'created_at',
                'updated_at',
            ],
        ],
        'installments' => [
            '*' => [
                'id',
                'customer_id',
                'status',
                'branch_id',
                'admin_id',
                'tariff_id',
                'type',
                'amount',
                'initial_fee',
                'sms_notify',
                'auto_pay',
                'auto_pay_by_pinfl',
                'is_blacklist',
                'period',
                'description',
                'cancellation_details',
                'starts_in',
                'next_pay_date',
                'created_at',
                'updated_at',
                'canceled_at',
                'deleted_at',
                'branch' => [
                    'id',
                    'name',
                    'address',
                    'inn',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'cards' => [
                    '*' => [
                        'id',
                        'customer_id',
                        'number',
                        'card_type',
                        'owner',
                        'expiry',
                        'is_connected',
                        'phone',
                        'status',
                        'balance',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'guarantors' => [
                    '*' => [
                        'id',
                        'installment_id',
                        'customer_id',
                        'any_card_charge',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
                'documents' => [
                    '*' => [
                        'id',
                        'type',
                        'url',
                        'name',
                        'original_name',
                        'mime',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create();
        Card::query()->forceDelete();
        Card::factory()->create(['customer_id' => $this->_customer->id]);
        Address::query()->forceDelete();
        Address::factory()->count(1)->create(['addressable_type' => Customer::class, 'addressable_id' => $this->_customer->id]);
        Phone::query()->forceDelete();
        foreach (Phone::factory()->count(1)->create() as $phone) {
            $this->_customer->phones()->attach($phone->id, ['type' => PhoneTypeEnum::randomValue()]);
        }
        Installment::query()->forceDelete();
        $installment = Installment::factory()->create(['customer_id' => $this->_customer->id, 'admin_id' => $this->createAdminUser()->id]);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        Card::query()->forceDelete();
        Card::factory()->create(['customer_id' => $installment->customer_id]);
        Card::factory()->create(['customer_id' => $guarantor->id]);
        $installment->cards()->attach(Card::first());
        Attachment::upload(UploadedFile::fake()->image('photo1.jpg'), AttachmentTypeEnum::AVATAR, $this->createAdminUser(), 'photo1.jpg');
        InstallmentComment::factory()->create(['installment_id' => $installment->id, 'admin_id' => $installment->admin_id]);
        Attachment::upload(UploadedFile::fake()->image('photo1.jpg'), AttachmentTypeEnum::INSTALLMENT_DOCUMENT, $installment, 'photo1.jpg');
        InstallmentGraph::factory()->create(['installment_id' => $installment->id]);
        Transaction::factory()->count(1)->create(['installment_id' => $installment->id, 'customer_id' => $installment->customer_id, 'admin_id' => $installment->admin_id]);
        Transaction::factory()->count(1)->create(['installment_id' => $installment->id, 'customer_id' => $guarantor->id, 'admin_id' => $installment->admin_id]);
    }

    #[Test]
    public function successWithPinfl()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['pinfl' => $this->_customer->pinfl])
            );

        $this->assertSuccessCustomerByPassportResponse($response);
    }

    #[Test]
    public function successWithPassportSeriesAndPassportNumber()
    {
        $inp = [
            'passport_series' => $this->_customer->passport_series,
            'passport_number' => (string) $this->_customer->passport_number,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($inp)
            );

        $this->assertSuccessCustomerByPassportResponse($response);
    }

    #[Test]
    public function errorWithPinfl()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['pinfl' => (string) rand(10000000000000, 99999999999999)])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.exists', ['attribute' => 'pinfl']), $response->json('errors.0.extensions.validation.pinfl.0'));

        $this->_customer->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['pinfl' => $this->_customer->pinfl])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.exists', ['attribute' => 'pinfl']), $response->json('errors.0.extensions.validation.pinfl.0'));
    }

    #[Test]
    public function errorWithPassportSeriesAndPassportNumber()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'passport_series' => 'AB',
                    'passport_number' => '1234567',
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.exists', ['attribute' => 'passport_number']), $response->json('errors.0.extensions.validation.passport_number.0'));

        $this->_customer->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'passport_series' => $this->_customer->passport_series,
                    'passport_number' => (string) $this->_customer->passport_number,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.exists', ['attribute' => 'passport_number']), $response->json('errors.0.extensions.validation.passport_number.0'));
    }

    #[Test]
    public function errorValidation()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery()
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'pinfl']), $response->json('errors.0.extensions.validation.pinfl.0'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'passport series']), $response->json('errors.0.extensions.validation.passport_series.0'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'passport number']), $response->json('errors.0.extensions.validation.passport_number.0'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'passport_series' => Str::upper(Str::random(2)),
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'passport number']), $response->json('errors.0.extensions.validation.passport_number.0'));
    }

    private function buildGraphQLQuery($variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query CustomerByPassport(
                $pinfl: String
                $passport_series: String
                $passport_number: String
            ) {
                customerByPassport(
                    pinfl: $pinfl
                    passport_series: $passport_series
                    passport_number: $passport_number
                ) {
                    ' . $this->convertIntoQuery($this->_structure) . '
                }
            }
        ';

        return $query;
    }

    private function assertSuccessCustomerByPassportResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'customerByPassport' => $this->_structure,
            ],
        ]);
        $customer = Customer::query()->find($response->json('data.customerByPassport.id'));
        $this->assertNotNull($customer);
        $this->assertEquals($customer->id, $response->json('data.customerByPassport.id'));
        $this->assertEquals($customer->pinfl, $response->json('data.customerByPassport.pinfl'));
        $this->assertEquals($customer->tin, $response->json('data.customerByPassport.tin'));
        $this->assertEquals($customer->passport_series, $response->json('data.customerByPassport.passport_series'));
        $this->assertEquals($customer->passport_number, $response->json('data.customerByPassport.passport_number'));
        $this->assertEquals($customer->passport_given_date?->format('d-m-Y'), $response->json('data.customerByPassport.passport_given_date'));
        $this->assertEquals($customer->passport_expiration_date?->format('d-m-Y'), $response->json('data.customerByPassport.passport_expiration_date'));
        $this->assertEquals($customer->passport_given_place, $response->json('data.customerByPassport.passport_given_place'));
        $this->assertEquals($customer->passport_citizenship, $response->json('data.customerByPassport.passport_citizenship'));
        $this->assertEquals($customer->first_name, $response->json('data.customerByPassport.first_name'));
        $this->assertEquals($customer->last_name, $response->json('data.customerByPassport.last_name'));
        $this->assertEquals($customer->middle_name, $response->json('data.customerByPassport.middle_name'));
        $this->assertEquals($customer->birth_date?->format('d-m-Y'), $response->json('data.customerByPassport.birth_date'));
        $this->assertEquals($customer->birth_place, $response->json('data.customerByPassport.birth_place'));
        $this->assertEquals($customer->gender->value, $response->json('data.customerByPassport.gender'));
        $this->assertEquals($customer->created_at->timestamp, $response->json('data.customerByPassport.created_at'));
        $this->assertEquals($customer->updated_at->timestamp, $response->json('data.customerByPassport.updated_at'));
        $this->assertEquals($customer?->deleted_at, $response->json('data.customerByPassport.deleted_at'));
    }

    private function convertIntoQuery(array $structure): string
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if ($key == '*') {
                    $structure[$key] = $this->convertIntoQuery($value);
                } else {
                    $structure[$key] = "{$key} {{$this->convertIntoQuery($value)}}";
                }
            }
        }

        return implode(' ', $structure);
    }
}
