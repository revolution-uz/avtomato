<?php

namespace Tests\GraphQL\Customer\Queries;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\CardStatusEnum;
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
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleCustomerGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

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
        Card::factory()->create(['customer_id' => $installment->customer_id, 'status' => CardStatusEnum::ACTIVE]);
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
    public function successSingleCustomer()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customer->id)
            );

        $this->assertSuccessSingleCustomerResponse($response);
    }

    #[Test]
    public function errorSingleCustomer()
    {
        // not found
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(99999999)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_customer->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customer->id)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $query['query'] = '
            query ($id: Int!) {
                singleCustomer(id: $id) {
                    id
                    pinfl
                    tin
                    passport_series
                    passport_number
                    passport_given_date
                    passport_expiration_date
                    passport_given_place
                    passport_citizenship
                    first_name
                    last_name
                    middle_name
                    birth_date
                    birth_place
                    gender
                    created_at
                    updated_at
                    deleted_at
                    cards {
                        id
                        customer_id
                        number
                        card_type
                        owner
                        expiry
                        is_connected
                        phone
                        status
                        balance
                        created_at
                        updated_at
                    }
                    addresses {
                        id
                        address
                        type
                        region_id
                        region {
                            id
                            name
                        }
                        district_id
                        district {
                            id
                            region_id
                            name
                        }
                        mahalla_id
                        mahalla {
                            id
                            district_id
                            name
                        }
                        created_at
                        updated_at
                        deleted_at
                    }
                    phones {
                        id
                        phone
                        type
                        comment
                        created_at
                        updated_at
                    }
                    installments {
                        amount
                        auto_pay
                        auto_pay_by_pinfl
                        branch {
                            address
                            created_at
                            deleted_at
                            id
                            inn
                            name
                            updated_at
                        }
                        branch_id
                        canceled_at
                        cancellation_details
                        created_at
                        customer_id
                        deleted_at
                        description
                        id
                        initial_fee
                        is_blacklist
                        next_pay_date
                        period
                        sms_notify
                        starts_in
                        status
                        tariff_id
                        type
                        updated_at
                        admin_id
                        cards {
                            balance
                            card_type
                            created_at
                            customer_id
                            expiry
                            id
                            is_connected
                            number
                            owner
                            phone
                            status
                            updated_at
                        }
                        comments {
                            comment
                            created_at
                            deleted_at
                            id
                            installment_id
                            updated_at
                            admin {
                                avatar {
                                    created_at
                                    deleted_at
                                    id
                                    mime
                                    name
                                    original_name
                                    type
                                    updated_at
                                    url
                                }
                                created_at
                                deleted_at
                                first_name
                                id
                                language
                                last_name
                                phone
                                role_id
                                status
                                template_theme {
                                    layout_name
                                }
                                updated_at
                                username
                            }
                            admin_id
                        }
                        documents {
                            created_at
                            deleted_at
                            id
                            mime
                            name
                            original_name
                            type
                            updated_at
                            url
                        }
                        graphs {
                            amount
                            id
                            installment_id
                            overdue_days
                            paid_amount
                            starts_in
                            status
                        }
                        guarantors {
                            any_card_charge
                            created_at
                            customer {
                                id
                            }
                            customer_id
                            deleted_at
                            id
                            installment_id
                            updated_at
                        }
                        tariff {
                            id
                            is_annual
                            max_amount
                            max_initial_fee
                            max_period
                            min_amount
                            min_initial_fee
                            min_period
                            name
                            percent
                        }
                        transactions {
                            admin_id
                            amount
                            branch_id
                            card_id
                            created_at
                            customer_id
                            external_id
                            id
                            installment_id
                            paid_at
                            transaction_type_id
                            updated_at
                            transaction_type {
                                created_at
                                deleted_at
                                id
                                is_system
                                name
                                name_en
                                name_ru
                                name_uz
                                updated_at
                            }
                        }
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSingleCustomerResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleCustomer' => [
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
                            'address',
                            'type',
                            'region_id',
                            'region' => [
                                'id',
                                'name',
                            ],
                            'district_id',
                            'district' => [
                                'id',
                                'region_id',
                                'name',
                            ],
                            'mahalla_id',
                            'mahalla' => [
                                'id',
                                'district_id',
                                'name',
                            ],
                            'created_at',
                            'updated_at',
                            'deleted_at',
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
                            'amount',
                            'auto_pay',
                            'auto_pay_by_pinfl',
                            'branch' => [
                                'address',
                                'created_at',
                                'deleted_at',
                                'id',
                                'inn',
                                'name',
                                'updated_at',
                            ],
                            'branch_id',
                            'canceled_at',
                            'cancellation_details',
                            'created_at',
                            'customer_id',
                            'deleted_at',
                            'description',
                            'id',
                            'initial_fee',
                            'is_blacklist',
                            'next_pay_date',
                            'period',
                            'sms_notify',
                            'starts_in',
                            'status',
                            'tariff_id',
                            'type',
                            'updated_at',
                            'admin_id',
                            'cards' => [
                                '*' => [
                                    'balance',
                                    'card_type',
                                    'created_at',
                                    'customer_id',
                                    'expiry',
                                    'id',
                                    'is_connected',
                                    'number',
                                    'owner',
                                    'phone',
                                    'status',
                                    'updated_at',
                                ],
                            ],
                            'comments' => [
                                '*' => [
                                    'comment',
                                    'created_at',
                                    'deleted_at',
                                    'id',
                                    'installment_id',
                                    'updated_at',
                                    'admin' => [
                                        'avatar' => [
                                            'created_at',
                                            'deleted_at',
                                            'id',
                                            'mime',
                                            'name',
                                            'original_name',
                                            'type',
                                            'updated_at',
                                            'url',
                                        ],
                                        'created_at',
                                        'deleted_at',
                                        'first_name',
                                        'id',
                                        'language',
                                        'last_name',
                                        'phone',
                                        'role_id',
                                        'status',
                                        'template_theme' => [
                                            'layout_name',
                                        ],
                                        'updated_at',
                                        'username',
                                    ],
                                    'admin_id',
                                ],
                            ],
                            'documents' => [
                                '*' => [
                                    'created_at',
                                    'deleted_at',
                                    'id',
                                    'mime',
                                    'name',
                                    'original_name',
                                    'type',
                                    'updated_at',
                                    'url',
                                ],
                            ],
                            'graphs' => [
                                '*' => [
                                    'amount',
                                    'id',
                                    'installment_id',
                                    'overdue_days',
                                    'paid_amount',
                                    'starts_in',
                                    'status',
                                ],
                            ],
                            'guarantors' => [
                                '*' => [
                                    'any_card_charge',
                                    'created_at',
                                    'customer' => [
                                        'id',
                                    ],
                                    'customer_id',
                                    'deleted_at',
                                    'id',
                                    'installment_id',
                                    'updated_at',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $customer = Customer::query()->find($response->json('data.singleCustomer.id'));
        $this->assertNotNull($customer);
        $this->assertEquals($customer->id, $response->json('data.singleCustomer.id'));
        $this->assertEquals($customer->pinfl, $response->json('data.singleCustomer.pinfl'));
        $this->assertEquals($customer->tin, $response->json('data.singleCustomer.tin'));
        $this->assertEquals($customer->passport_series, $response->json('data.singleCustomer.passport_series'));
        $this->assertEquals($customer->passport_number, $response->json('data.singleCustomer.passport_number'));
        $this->assertEquals($customer->passport_given_date?->format('d-m-Y'), $response->json('data.singleCustomer.passport_given_date'));
        $this->assertEquals($customer->passport_expiration_date?->format('d-m-Y'), $response->json('data.singleCustomer.passport_expiration_date'));
        $this->assertEquals($customer->passport_given_place, $response->json('data.singleCustomer.passport_given_place'));
        $this->assertEquals($customer->passport_citizenship, $response->json('data.singleCustomer.passport_citizenship'));
        $this->assertEquals($customer->first_name, $response->json('data.singleCustomer.first_name'));
        $this->assertEquals($customer->last_name, $response->json('data.singleCustomer.last_name'));
        $this->assertEquals($customer->middle_name, $response->json('data.singleCustomer.middle_name'));
        $this->assertEquals($customer->birth_date?->format('d-m-Y'), $response->json('data.singleCustomer.birth_date'));
        $this->assertEquals($customer->birth_place, $response->json('data.singleCustomer.birth_place'));
        $this->assertEquals($customer->gender->value, $response->json('data.singleCustomer.gender'));
        $this->assertEquals($customer->created_at->timestamp, $response->json('data.singleCustomer.created_at'));
        $this->assertEquals($customer->updated_at->timestamp, $response->json('data.singleCustomer.updated_at'));
        $this->assertEquals($customer->deleted_at, $response->json('data.singleCustomer.deleted_at'));

        // cards
        $this->assertEquals($customer->cards[0]->id, $response->json('data.singleCustomer.cards.0.id'));
        $this->assertEquals($customer->cards[0]->customer_id, $response->json('data.singleCustomer.cards.0.customer_id'));
        $this->assertEquals($customer->cards[0]->pan, $response->json('data.singleCustomer.cards.0.number'));
        $this->assertEquals($customer->cards[0]->card_type, $response->json('data.singleCustomer.cards.0.card_type'));
        $this->assertEquals($customer->cards[0]->owner, $response->json('data.singleCustomer.cards.0.owner'));
        $this->assertEquals($customer->cards[0]->expiry, $response->json('data.singleCustomer.cards.0.expiry'));
        $this->assertEquals($customer->cards[0]->is_connected, $response->json('data.singleCustomer.cards.0.is_connected'));
        $this->assertEquals($customer->cards[0]->phone, $response->json('data.singleCustomer.cards.0.phone'));
        $this->assertEquals($customer->cards[0]->status->value, $response->json('data.singleCustomer.cards.0.status'));
        $this->assertEquals($customer->cards[0]->balance, $response->json('data.singleCustomer.cards.0.balance'));
        $this->assertEquals($customer->cards[0]->created_at->timestamp, $response->json('data.singleCustomer.cards.0.created_at'));
        $this->assertEquals($customer->cards[0]->updated_at->timestamp, $response->json('data.singleCustomer.cards.0.updated_at'));

        // addresses
        $this->assertEquals($customer->addresses[0]->address, $response->json('data.singleCustomer.addresses.0.address'));
        $this->assertEquals($customer->addresses[0]->type->value, $response->json('data.singleCustomer.addresses.0.type'));
        $this->assertEquals($customer->addresses[0]->region_id, $response->json('data.singleCustomer.addresses.0.region_id'));
        $this->assertEquals($customer->addresses[0]->region->id, $response->json('data.singleCustomer.addresses.0.region.id'));
        $this->assertEquals($customer->addresses[0]->region->name, $response->json('data.singleCustomer.addresses.0.region.name'));
        $this->assertEquals($customer->addresses[0]->district_id, $response->json('data.singleCustomer.addresses.0.district_id'));
        $this->assertEquals($customer->addresses[0]->district->id, $response->json('data.singleCustomer.addresses.0.district.id'));
        $this->assertEquals($customer->addresses[0]->district->region_id, $response->json('data.singleCustomer.addresses.0.district.region_id'));
        $this->assertEquals($customer->addresses[0]->district->name, $response->json('data.singleCustomer.addresses.0.district.name'));
        $this->assertEquals($customer->addresses[0]->mahalla_id, $response->json('data.singleCustomer.addresses.0.mahalla_id'));
        $this->assertEquals($customer->addresses[0]->mahalla->district_id, $response->json('data.singleCustomer.addresses.0.mahalla.district_id'));
        $this->assertEquals($customer->addresses[0]->mahalla->name, $response->json('data.singleCustomer.addresses.0.mahalla.name'));
        $this->assertEquals($customer->addresses[0]->created_at->timestamp, $response->json('data.singleCustomer.addresses.0.updated_at'));
        $this->assertEquals($customer->addresses[0]->updated_at->timestamp, $response->json('data.singleCustomer.addresses.0.updated_at'));
        $this->assertEquals($customer->addresses[0]->deleted_at, $response->json('data.singleCustomer.addresses.0.deleted_at'));

        // phones
        $this->assertEquals($customer->phones[0]->id, $response->json('data.singleCustomer.phones.0.id'));
        $this->assertEquals($customer->phones[0]->phone, $response->json('data.singleCustomer.phones.0.phone'));
        $this->assertEquals($customer->phones[0]->pivot->type, $response->json('data.singleCustomer.phones.0.type'));
        $this->assertEquals($customer->phones[0]->pivot->comment, $response->json('data.singleCustomer.phones.0.comment'));
        $this->assertEquals($customer->phones[0]->pivot->created_at->timestamp, $response->json('data.singleCustomer.phones.0.created_at'));
        $this->assertEquals($customer->phones[0]->pivot->updated_at->timestamp, $response->json('data.singleCustomer.phones.0.updated_at'));

        // installments
        $this->assertEquals($customer->installments[0]->amount, $response->json('data.singleCustomer.installments.0.amount'));
        $this->assertEquals($customer->installments[0]->auto_pay, $response->json('data.singleCustomer.installments.0.auto_pay'));
        $this->assertEquals($customer->installments[0]->any_card_charge, $response->json('data.singleCustomer.installments.0.auto_pay_by_pinfl'));
        $this->assertEquals($customer->installments[0]->branch->address, $response->json('data.singleCustomer.installments.0.branch.address'));
        $this->assertEquals($customer->installments[0]->branch->created_at->timestamp, $response->json('data.singleCustomer.installments.0.branch.created_at'));
        $this->assertEquals($customer->installments[0]->branch->deleted_at, $response->json('data.singleCustomer.installments.0.branch.deleted_at'));
        $this->assertEquals($customer->installments[0]->branch->id, $response->json('data.singleCustomer.installments.0.branch.id'));
        $this->assertEquals($customer->installments[0]->branch->inn, $response->json('data.singleCustomer.installments.0.branch.inn'));
        $this->assertEquals($customer->installments[0]->branch->name, $response->json('data.singleCustomer.installments.0.branch.name'));
        $this->assertEquals($customer->installments[0]->branch->updated_at->timestamp, $response->json('data.singleCustomer.installments.0.branch.updated_at'));
        $this->assertEquals($customer->installments[0]->branch_id, $response->json('data.singleCustomer.installments.0.branch_id'));
        $this->assertEquals($customer->installments[0]->canceled_at?->timestamp, $response->json('data.singleCustomer.installments.0.canceled_at'));
        $this->assertEquals($customer->installments[0]->cancellation_details, $response->json('data.singleCustomer.installments.0.cancellation_details'));
        $this->assertEquals($customer->installments[0]->created_at->timestamp, $response->json('data.singleCustomer.installments.0.created_at'));
        $this->assertEquals($customer->installments[0]->customer_id, $response->json('data.singleCustomer.installments.0.customer_id'));
        $this->assertEquals($customer->installments[0]->deleted_at, $response->json('data.singleCustomer.installments.0.deleted_at'));
        $this->assertEquals($customer->installments[0]->description, $response->json('data.singleCustomer.installments.0.description'));
        $this->assertEquals($customer->installments[0]->id, $response->json('data.singleCustomer.installments.0.id'));
        $this->assertEquals($customer->installments[0]->initial_fee, $response->json('data.singleCustomer.installments.0.initial_fee'));
        $this->assertEquals($customer->installments[0]->is_blacklist, $response->json('data.singleCustomer.installments.0.is_blacklist'));
        $this->assertEquals($customer->installments[0]->next_pay_date?->timestamp, $response->json('data.singleCustomer.installments.0.next_pay_date'));
        $this->assertEquals($customer->installments[0]->period, $response->json('data.singleCustomer.installments.0.period'));
        $this->assertEquals($customer->installments[0]->sms_notify, $response->json('data.singleCustomer.installments.0.sms_notify'));
        $this->assertEquals($customer->installments[0]->starts_in?->timestamp, $response->json('data.singleCustomer.installments.0.starts_in'));
        $this->assertEquals($customer->installments[0]->status->value, $response->json('data.singleCustomer.installments.0.status'));
        $this->assertEquals($customer->installments[0]->tariff_id, $response->json('data.singleCustomer.installments.0.tariff_id'));
        $this->assertEquals($customer->installments[0]->type->value, $response->json('data.singleCustomer.installments.0.type'));
        $this->assertEquals($customer->installments[0]->updated_at->timestamp, $response->json('data.singleCustomer.installments.0.updated_at'));
        $this->assertEquals($customer->installments[0]->admin_id, $response->json('data.singleCustomer.installments.0.admin_id'));

        // installment cards
        $this->assertEquals($customer->installments[0]->cards[0]->balance, $response->json('data.singleCustomer.installments.0.cards.0.balance'));
        $this->assertEquals($customer->installments[0]->cards[0]->card_type, $response->json('data.singleCustomer.installments.0.cards.0.card_type'));
        $this->assertEquals($customer->installments[0]->cards[0]->created_at->timestamp, $response->json('data.singleCustomer.installments.0.cards.0.created_at'));
        $this->assertEquals($customer->installments[0]->cards[0]->customer_id, $response->json('data.singleCustomer.installments.0.cards.0.customer_id'));
        $this->assertEquals($customer->installments[0]->cards[0]->expiry, $response->json('data.singleCustomer.installments.0.cards.0.expiry'));
        $this->assertEquals($customer->installments[0]->cards[0]->id, $response->json('data.singleCustomer.installments.0.cards.0.id'));
        $this->assertEquals($customer->installments[0]->cards[0]->is_connected, $response->json('data.singleCustomer.installments.0.cards.0.is_connected'));
        $this->assertEquals($customer->installments[0]->cards[0]->pan, $response->json('data.singleCustomer.installments.0.cards.0.number'));
        $this->assertEquals($customer->installments[0]->cards[0]->owner, $response->json('data.singleCustomer.installments.0.cards.0.owner'));
        $this->assertEquals($customer->installments[0]->cards[0]->phone, $response->json('data.singleCustomer.installments.0.cards.0.phone'));
        $this->assertEquals($customer->installments[0]->cards[0]->status->value, $response->json('data.singleCustomer.installments.0.cards.0.status'));
        $this->assertEquals($customer->installments[0]->cards[0]->updated_at->timestamp, $response->json('data.singleCustomer.installments.0.cards.0.updated_at'));

        // installment guarantors
        $this->assertEquals($customer->installments[0]->guarantorList[0]->any_card_charge, $response->json('data.singleCustomer.installments.0.guarantors.0.any_card_charge'));
        $this->assertEquals($customer->installments[0]->guarantorList[0]->created_at->timestamp, $response->json('data.singleCustomer.installments.0.guarantors.0.created_at'));
        $this->assertEquals($customer->installments[0]->guarantorList[0]->customer->id, $response->json('data.singleCustomer.installments.0.guarantors.0.customer.id'));
        $this->assertEquals($customer->installments[0]->guarantorList[0]->customer_id, $response->json('data.singleCustomer.installments.0.guarantors.0.customer_id'));
        $this->assertEquals($customer->installments[0]->guarantorList[0]->deleted_at, $response->json('data.singleCustomer.installments.0.guarantors.0.deleted_at'));
        $this->assertEquals($customer->installments[0]->guarantorList[0]->id, $response->json('data.singleCustomer.installments.0.guarantors.0.id'));
        $this->assertEquals($customer->installments[0]->guarantorList[0]->installment_id, $response->json('data.singleCustomer.installments.0.guarantors.0.installment_id'));
        $this->assertEquals($customer->installments[0]->guarantorList[0]->updated_at->timestamp, $response->json('data.singleCustomer.installments.0.guarantors.0.updated_at'));

        // installment documents
        $this->assertEquals($customer->installments[0]->documents[0]->created_at->timestamp, $response->json('data.singleCustomer.installments.0.documents.2.created_at'));
        $this->assertEquals($customer->installments[0]->documents[0]->deleted_at, $response->json('data.singleCustomer.installments.0.documents.2.deleted_at'));
        $this->assertEquals($customer->installments[0]->documents[0]->id, $response->json('data.singleCustomer.installments.0.documents.2.id'));
        $this->assertEquals($customer->installments[0]->documents[0]->mime, $response->json('data.singleCustomer.installments.0.documents.2.mime'));
        $this->assertEquals($customer->installments[0]->documents[0]->name, $response->json('data.singleCustomer.installments.0.documents.2.name'));
        $this->assertEquals($customer->installments[0]->documents[0]->original_name, $response->json('data.singleCustomer.installments.0.documents.2.original_name'));
        $this->assertEquals($customer->installments[0]->documents[0]->type->value, $response->json('data.singleCustomer.installments.0.documents.2.type'));
        $this->assertEquals($customer->installments[0]->documents[0]->updated_at->timestamp, $response->json('data.singleCustomer.installments.0.documents.2.updated_at'));
        $this->assertEquals($customer->installments[0]->documents[0]->url, $response->json('data.singleCustomer.installments.0.documents.2.url'));
    }
}
