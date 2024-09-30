<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\InstallmentGraphHelper;
use App\Models\Company\Address;
use App\Models\Company\Attachment;
use App\Models\Company\Branch;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentComment;
use App\Models\Company\Phone;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleInstallmentGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Installment $_installment;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        $this->_installment = Installment::factory()->create(['starts_in' => now()->addDays(5)]);
        Attachment::upload(UploadedFile::fake()->image('photo1.jpg'), AttachmentTypeEnum::AVATAR, $this->_installment->admin, 'photo1.jpg');
        $guarantor = Customer::factory()->create();
        $this->_installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        Card::query()->forceDelete();
        Card::factory()->create(['customer_id' => $this->_installment->customer_id, 'status' => CardStatusEnum::ACTIVE]);
        Card::factory()->create(['customer_id' => $guarantor->id, 'status' => CardStatusEnum::ACTIVE]);
        $this->_installment->cards()->attach(Card::first());
        Address::query()->forceDelete();
        Address::factory()->count(1)->create(['addressable_type' => Customer::class, 'addressable_id' => $this->_installment->customer_id]);
        Address::factory()->count(1)->create(['addressable_type' => Customer::class, 'addressable_id' => $guarantor->id]);
        Phone::query()->forceDelete();
        /** @var Phone $phone */
        foreach (Phone::factory()->count(1)->create() as $phone) {
            $this->_installment->customer->phones()->attach($phone->id, ['type' => Arr::random(PhoneTypeEnum::customerValues())]);
            $guarantor->phones()->attach($phone->id, ['type' => Arr::random(PhoneTypeEnum::customerValues())]);
        }
        InstallmentComment::factory()->create(['installment_id' => $this->_installment->id, 'admin_id' => $this->_installment->admin_id]);
        Attachment::upload(UploadedFile::fake()->image('photo1.jpg'), AttachmentTypeEnum::INSTALLMENT_DOCUMENT, $this->_installment, 'photo1.jpg');
        Transaction::factory()->create(['installment_id' => $this->_installment->id, 'customer_id' => $this->_installment->customer_id, 'admin_id' => $this->_installment->admin_id]);
        Transaction::factory()->create(['installment_id' => $this->_installment->id, 'customer_id' => $guarantor->id, 'admin_id' => $this->_installment->admin_id, 'is_initial_fee' => true]);
    }

    #[Test]
    public function successSingleInstallment()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($this->_installment->id)
            );

        $this->assertSuccessSingleInstallmentResponse($response);
    }

    #[Test]
    public function errorSingleInstallment()
    {
        // not found
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(99999999)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_installment->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($this->_installment->id)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $this->_installment->update(['branch_id' => $branches[1]->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_installment->id));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_installment->id));
        $this->assertSuccessSingleInstallmentResponse($response);
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $adminFields = '
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
        ';
        $customerFields = '
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
        ';
        $cardFields = "
            balance
            card_type
            created_at
            customer {
                {$customerFields}
            }
            customer_id
            expiry
            id
            is_connected
            number
            owner
            phone
            status
            updated_at
        ";
        $branchFields = '
            address
            created_at
            deleted_at
            id
            inn
            name
            updated_at
        ';
        $query['query'] = "
            query (\$id: Int!) {
                singleInstallment(id: \$id) {
                    amount
                    auto_pay
                    auto_pay_by_pinfl
                    branch {
                        {$branchFields}
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
                    initial_fee_transaction {
                        id
                        branch_id
                        admin_id
                        installment_id
                        is_initial_fee
                        customer_id
                        transaction_type_id
                        card_id
                        card_pan
                        external_id
                        amount
                        status
                        paid_at
                        created_at
                        updated_at
                    }
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
                        {$cardFields}
                    }
                    comments {
                        comment
                        created_at
                        deleted_at
                        id
                        installment_id
                        updated_at
                        admin {
                            {$adminFields}
                        }
                        admin_id
                    }
                    customer {
                        {$customerFields}
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
                        overdue_days
                        paid_amount
                        starts_in
                        status
                    }
                    guarantors {
                        any_card_charge
                        created_at
                        customer {
                            {$customerFields}
                        }
                        customer_id
                        deleted_at
                        id
                        installment_id
                        updated_at
                    }
                    tariff {
                        id
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
                        admin {
                            {$adminFields}
                        }
                        branch {
                            {$branchFields}
                        }
                        card {
                            {$cardFields}
                        }
                        customer {
                            {$customerFields}
                        }
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
                    admin {
                        {$adminFields}
                    }
                }
            }
        ";

        return $query;
    }

    private function assertSuccessSingleInstallmentResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $adminFields = [
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
        ];
        $customerFields = [
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
        ];
        $branchFields = [
            'address',
            'created_at',
            'deleted_at',
            'id',
            'inn',
            'name',
            'updated_at',
        ];
        $cardFields = [
            'balance',
            'card_type',
            'created_at',
            'customer' => $customerFields,
            'customer_id',
            'expiry',
            'id',
            'is_connected',
            'number',
            'owner',
            'phone',
            'status',
            'updated_at',
        ];
        $response->assertJsonStructure([
            'data' => [
                'singleInstallment' => [
                    'amount',
                    'auto_pay',
                    'auto_pay_by_pinfl',
                    'branch' => $branchFields,
                    'branch_id',
                    'canceled_at',
                    'cancellation_details',
                    'created_at',
                    'customer_id',
                    'deleted_at',
                    'description',
                    'id',
                    'initial_fee',
                    'initial_fee_transaction' => [
                        'id',
                        'branch_id',
                        'admin_id',
                        'installment_id',
                        'is_initial_fee',
                        'customer_id',
                        'transaction_type_id',
                        'card_id',
                        'card_pan',
                        'external_id',
                        'amount',
                        'status',
                        'paid_at',
                        'created_at',
                        'updated_at',
                    ],
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
                    'comments' => [
                        '*' => [
                            'comment',
                            'created_at',
                            'deleted_at',
                            'id',
                            'installment_id',
                            'updated_at',
                            'admin_id',
                            'admin' => $adminFields,
                        ],
                    ],
                    'customer' => $customerFields,
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
                            'customer_id',
                            'deleted_at',
                            'id',
                            'installment_id',
                            'updated_at',
                            'customer' => $customerFields,
                        ],
                    ],
                    'tariff' => [
                        'id',
                        'max_amount',
                        'max_initial_fee',
                        'max_period',
                        'min_amount',
                        'min_initial_fee',
                        'min_period',
                        'name',
                        'percent',
                    ],
                    'transactions' => [
                        '*' => [
                            'admin_id',
                            'amount',
                            'branch_id',
                            'card_id',
                            'created_at',
                            'customer_id',
                            'external_id',
                            'id',
                            'installment_id',
                            'paid_at',
                            'transaction_type_id',
                            'updated_at',
                            'admin' => $adminFields,
                            'branch' => $branchFields,
                            'card' => $cardFields,
                            'customer' => $customerFields,
                            'transaction_type' => [
                                'created_at',
                                'deleted_at',
                                'id',
                                'is_system',
                                'name',
                                'name_en',
                                'name_ru',
                                'name_uz',
                                'updated_at',
                            ],
                        ],
                    ],
                    'admin' => $adminFields,
                ],
            ],
        ]);
        $installment = Installment::query()->find($response->json('data.singleInstallment.id'));
        $this->assertNotNull($installment);
        $this->assertEquals($installment->amount, $response->json('data.singleInstallment.amount'));
        $this->assertEquals($installment->auto_pay, $response->json('data.singleInstallment.auto_pay'));
        $this->assertEquals($installment->any_card_charge, $response->json('data.singleInstallment.auto_pay_by_pinfl'));
        $this->assertEquals($installment->branch_id, $response->json('data.singleInstallment.branch.id'));
        $this->assertEquals($installment->canceled_at?->format('d-m-Y H:i:s'), $response->json('data.singleInstallment.canceled_at'));
        $this->assertEquals($installment->cancellation_details, $response->json('data.singleInstallment.cancellation_details'));
        $this->assertEquals($installment->created_at->timestamp, $response->json('data.singleInstallment.created_at'));
        $this->assertEquals($installment->deleted_at, $response->json('data.singleInstallment.deleted_at'));
        $this->assertEquals($installment->description, $response->json('data.singleInstallment.description'));
        $this->assertEquals($installment->id, $response->json('data.singleInstallment.id'));
        $this->assertEquals($installment->initial_fee, $response->json('data.singleInstallment.initial_fee'));
        $this->assertEquals($installment->is_blacklist, $response->json('data.singleInstallment.is_blacklist'));
        $this->assertEquals($installment->period, $response->json('data.singleInstallment.period'));
        $this->assertEquals($installment->sms_notify, $response->json('data.singleInstallment.sms_notify'));
        $this->assertEquals($installment->starts_in?->timestamp, $response->json('data.singleInstallment.starts_in'));
        $this->assertEquals($installment->status->value, $response->json('data.singleInstallment.status'));
        $this->assertEquals($installment->tariff_id, $response->json('data.singleInstallment.tariff_id'));
        $this->assertEquals($installment->type->value, $response->json('data.singleInstallment.type'));
        $this->assertEquals($installment->updated_at->timestamp, $response->json('data.singleInstallment.updated_at'));
        $this->assertEquals($installment->admin_id, $response->json('data.singleInstallment.admin_id'));

        // branch
        $this->assertEquals($installment->branch->address, $response->json('data.singleInstallment.branch.address'));
        $this->assertEquals($installment->branch->created_at->timestamp, $response->json('data.singleInstallment.branch.created_at'));
        $this->assertEquals($installment->branch->deleted_at, $response->json('data.singleInstallment.branch.deleted_at'));
        $this->assertEquals($installment->branch->id, $response->json('data.singleInstallment.branch.id'));
        $this->assertEquals($installment->branch->inn, $response->json('data.singleInstallment.branch.inn'));
        $this->assertEquals($installment->branch->name, $response->json('data.singleInstallment.branch.name'));
        $this->assertEquals($installment->branch->updated_at->timestamp, $response->json('data.singleInstallment.branch.updated_at'));

        // cards
        $this->assertEquals($installment->cards->count(), count($response->json('data.singleInstallment.cards')));
        foreach ($installment->cards as $card) {
            $cardResponse = collect($response->json('data.singleInstallment.cards'))->first(fn ($item) => $item['id'] === $card->id);
            $this->assertNotNull($cardResponse);
            $this->assertEquals($card->balance, $cardResponse['balance']);
            $this->assertEquals($card->card_type, $cardResponse['card_type']);
            $this->assertEquals($card->created_at->timestamp, $cardResponse['created_at']);
            $this->assertEquals($card->customer_id, $cardResponse['customer_id']);
            $this->assertEquals($card->expiry, $cardResponse['expiry']);
            $this->assertEquals($card->id, $cardResponse['id']);
            $this->assertEquals($card->is_connected, $cardResponse['is_connected']);
            $this->assertEquals($card->pan, $cardResponse['number']);
            $this->assertEquals($card->owner, $cardResponse['owner']);
            $this->assertEquals($card->phone, $cardResponse['phone']);
            $this->assertEquals($card->status->value, $cardResponse['status']);
            $this->assertEquals($card->updated_at->timestamp, $cardResponse['updated_at']);
        }

        // comments
        $this->assertEquals($installment->comments->count(), count($response->json('data.singleInstallment.comments')));
        foreach ($installment->comments as $comment) {
            $commentResponse = collect($response->json('data.singleInstallment.comments'))->first(fn ($item) => $item['id'] === $comment->id);
            $this->assertNotNull($commentResponse);
            $this->assertEquals($comment->comment, $commentResponse['comment']);
            $this->assertEquals($comment->created_at->timestamp, $commentResponse['created_at']);
            $this->assertEquals($comment->deleted_at, $commentResponse['deleted_at']);
            $this->assertEquals($comment->id, $commentResponse['id']);
            $this->assertEquals($comment->installment_id, $commentResponse['installment_id']);
            $this->assertEquals($comment->updated_at->timestamp, $commentResponse['updated_at']);
            $this->assertEquals($comment->admin_id, $commentResponse['admin_id']);
            $this->assertEquals($comment->admin->avatar->url, $commentResponse['admin']['avatar']['url']);
            $this->assertEquals($comment->admin->created_at->timestamp, $commentResponse['admin']['created_at']);
            $this->assertEquals($comment->admin->deleted_at, $commentResponse['admin']['deleted_at']);
            $this->assertEquals($comment->admin->first_name, $commentResponse['admin']['first_name']);
            $this->assertEquals($comment->admin->id, $commentResponse['admin']['id']);
            $this->assertEquals($comment->admin->language, $commentResponse['admin']['language']);
            $this->assertEquals($comment->admin->last_name, $commentResponse['admin']['last_name']);
            $this->assertEquals($comment->admin->phone, $commentResponse['admin']['phone']);
            $this->assertEquals($comment->admin->role_id, $commentResponse['admin']['role_id']);
            $this->assertEquals($comment->admin->status->value, $commentResponse['admin']['status']);
            $this->assertEquals($comment->admin->template_theme->layout_name, $commentResponse['admin']['template_theme']['layout_name']);
            $this->assertEquals($comment->admin->updated_at->timestamp, $commentResponse['admin']['updated_at']);
            $this->assertEquals($comment->admin->username, $commentResponse['admin']['username']);
        }

        $this->assertCustomerFields($response->json('data.singleInstallment.customer'));

        // documents
        $this->assertEquals($installment->documents->count() + 2, count($response->json('data.singleInstallment.documents')));
        foreach ($installment->documents as $document) {
            $documentResponse = collect($response->json('data.singleInstallment.documents'))->first(fn ($item) => $item['id'] === $document->id);
            $this->assertNotNull($documentResponse);
            $this->assertEquals($document->created_at->timestamp, $documentResponse['created_at']);
            $this->assertEquals($document->deleted_at, $documentResponse['deleted_at']);
            $this->assertEquals($document->id, $documentResponse['id']);
            $this->assertEquals($document->mime, $documentResponse['mime']);
            $this->assertEquals($document->name, $documentResponse['name']);
            $this->assertEquals($document->original_name, $documentResponse['original_name']);
            $this->assertEquals($document->type->value, $documentResponse['type']);
            $this->assertEquals($document->updated_at->timestamp, $documentResponse['updated_at']);
            $this->assertEquals($document->url, $documentResponse['url']);
        }

        // graphs
        $this->assertEquals(count(InstallmentGraphHelper::getGraphs($installment)), count($response->json('data.singleInstallment.graphs')));
        foreach (InstallmentGraphHelper::getGraphs($installment) as $graph) {
            $graphResponse = collect($response->json('data.singleInstallment.graphs'))->first(fn ($item) => $item['id'] === $graph->id);
            $this->assertNotNull($graphResponse);
            $this->assertEquals($graph->amount, $graphResponse['amount']);
            $this->assertEquals($graph->id, $graphResponse['id']);
            $this->assertEquals($graph->overdue_days, $graphResponse['overdue_days']);
            $this->assertEquals($graph->paid_amount, $graphResponse['paid_amount']);
            $this->assertEquals($graph->starts_in->timestamp, $graphResponse['starts_in']);
            $this->assertEquals($graph->status->value, $graphResponse['status']);
        }

        // guarantors
        $this->assertEquals($installment->guarantors->count(), count($response->json('data.singleInstallment.guarantors')));
        foreach ($installment->guarantorList as $guarantor) {
            $guarantorResponse = collect($response->json('data.singleInstallment.guarantors'))->first(fn ($item) => $item['id'] === $guarantor->id);
            $this->assertNotNull($guarantorResponse);
            $this->assertEquals($guarantor->any_card_charge, $guarantorResponse['any_card_charge']);
            $this->assertEquals($guarantor->created_at->timestamp, $guarantorResponse['created_at']);
            $this->assertEquals($guarantor->customer_id, $guarantorResponse['customer_id']);
            $this->assertEquals($guarantor->deleted_at, $guarantorResponse['deleted_at']);
            $this->assertEquals($guarantor->id, $guarantorResponse['id']);
            $this->assertEquals($guarantor->installment_id, $guarantorResponse['installment_id']);
            $this->assertEquals($guarantor->updated_at->timestamp, $guarantorResponse['updated_at']);
            $this->assertCustomerFields($guarantorResponse['customer']);
        }

        // tariff
        $this->assertEquals($installment->tariff->id, $response->json('data.singleInstallment.tariff.id'));
        $this->assertEquals($installment->tariff->max_amount, $response->json('data.singleInstallment.tariff.max_amount'));
        $this->assertEquals($installment->tariff->max_initial_fee, $response->json('data.singleInstallment.tariff.max_initial_fee'));
        $this->assertEquals($installment->tariff->max_period, $response->json('data.singleInstallment.tariff.max_period'));
        $this->assertEquals($installment->tariff->min_amount, $response->json('data.singleInstallment.tariff.min_amount'));
        $this->assertEquals($installment->tariff->min_initial_fee, $response->json('data.singleInstallment.tariff.min_initial_fee'));
        $this->assertEquals($installment->tariff->min_period, $response->json('data.singleInstallment.tariff.min_period'));
        $this->assertEquals($installment->tariff->name, $response->json('data.singleInstallment.tariff.name'));
        $this->assertEquals($installment->tariff->percent, $response->json('data.singleInstallment.tariff.percent'));

        // phones
        $this->assertEquals($installment->customer->phones->count(), count($response->json('data.singleInstallment.customer.phones')));
        foreach ($installment->customer->phones as $phone) {
            $phoneResponse = collect($response->json('data.singleInstallment.customer.phones'))->first(fn ($item) => $item['id'] === $phone->id);
            $this->assertNotNull($phoneResponse);
            $this->assertEquals($phone->id, $phoneResponse['id']);
            $this->assertEquals($phone->phone, $phoneResponse['phone']);
            $this->assertEquals($phone->pivot->type, $phoneResponse['type']);
            $this->assertEquals($phone->pivot->comment, $phoneResponse['comment']);
            $this->assertEquals($phone->created_at->timestamp, $phoneResponse['created_at']);
            $this->assertEquals($phone->updated_at->timestamp, $phoneResponse['updated_at']);
        }

        // transactions
        $this->assertEquals($installment->transactions->count(), count($response->json('data.singleInstallment.transactions')));
        foreach ($installment->transactions as $transaction) {
            $transactionResponse = collect($response->json('data.singleInstallment.transactions'))->first(fn ($item) => $item['id'] === $transaction->id);
            $this->assertNotNull($transactionResponse);
            $this->assertEquals($transaction->admin_id, $transactionResponse['admin_id']);
            $this->assertEquals($transaction->amount, $transactionResponse['amount']);
            $this->assertEquals($transaction->branch_id, $transactionResponse['branch_id']);
            $this->assertEquals($transaction->card_id, $transactionResponse['card_id']);
            $this->assertEquals($transaction->created_at->timestamp, $transactionResponse['created_at']);
            $this->assertEquals($transaction->customer_id, $transactionResponse['customer_id']);
            $this->assertEquals($transaction->external_id, $transactionResponse['external_id']);
            $this->assertEquals($transaction->id, $transactionResponse['id']);
            $this->assertEquals($transaction->installment_id, $transactionResponse['installment_id']);
            $this->assertEquals($transaction->paid_at?->timestamp, $transactionResponse['paid_at']);
            $this->assertEquals($transaction->transaction_type_id, $transactionResponse['transaction_type_id']);
            $this->assertEquals($transaction->updated_at->timestamp, $transactionResponse['updated_at']);

            // admin
            $this->assertEquals($transaction->admin->avatar->url, $transactionResponse['admin']['avatar']['url']);
            $this->assertEquals($transaction->admin->created_at->timestamp, $transactionResponse['admin']['created_at']);
            $this->assertEquals($transaction->admin->deleted_at, $transactionResponse['admin']['deleted_at']);
            $this->assertEquals($transaction->admin->first_name, $transactionResponse['admin']['first_name']);
            $this->assertEquals($transaction->admin->id, $transactionResponse['admin']['id']);
            $this->assertEquals($transaction->admin->language, $transactionResponse['admin']['language']);
            $this->assertEquals($transaction->admin->last_name, $transactionResponse['admin']['last_name']);
            $this->assertEquals($transaction->admin->phone, $transactionResponse['admin']['phone']);
            $this->assertEquals($transaction->admin->role_id, $transactionResponse['admin']['role_id']);
            $this->assertEquals($transaction->admin->status->value, $transactionResponse['admin']['status']);
            $this->assertEquals($transaction->admin->template_theme->layout_name, $transactionResponse['admin']['template_theme']['layout_name']);
            $this->assertEquals($transaction->admin->updated_at->timestamp, $transactionResponse['admin']['updated_at']);
            $this->assertEquals($transaction->admin->username, $transactionResponse['admin']['username']);

            // card
            $this->assertEquals($transaction->card->balance, $transactionResponse['card']['balance']);
            $this->assertEquals($transaction->card->card_type, $transactionResponse['card']['card_type']);
            $this->assertEquals($transaction->card->created_at->timestamp, $transactionResponse['card']['created_at']);
            $this->assertEquals($transaction->card->customer_id, $transactionResponse['card']['customer_id']);
            $this->assertEquals($transaction->card->expiry, $transactionResponse['card']['expiry']);
            $this->assertEquals($transaction->card->id, $transactionResponse['card']['id']);
            $this->assertEquals($transaction->card->is_connected, $transactionResponse['card']['is_connected']);
            $this->assertEquals($transaction->card->pan, $transactionResponse['card']['number']);
            $this->assertEquals($transaction->card->owner, $transactionResponse['card']['owner']);
            $this->assertEquals($transaction->card->phone, $transactionResponse['card']['phone']);

            // transaction_type
            $this->assertEquals($transaction->transactionType->created_at->timestamp, $transactionResponse['transaction_type']['created_at']);
            $this->assertEquals($transaction->transactionType->deleted_at, $transactionResponse['transaction_type']['deleted_at']);
            $this->assertEquals($transaction->transactionType->id, $transactionResponse['transaction_type']['id']);
            $this->assertEquals($transaction->transactionType->is_system, $transactionResponse['transaction_type']['is_system']);
            $this->assertEquals($transaction->transactionType->name, $transactionResponse['transaction_type']['name']);
            $this->assertEquals($transaction->transactionType->name_en, $transactionResponse['transaction_type']['name_en']);
            $this->assertEquals($transaction->transactionType->name_ru, $transactionResponse['transaction_type']['name_ru']);
            $this->assertEquals($transaction->transactionType->name_uz, $transactionResponse['transaction_type']['name_uz']);
            $this->assertEquals($transaction->transactionType->updated_at->timestamp, $transactionResponse['transaction_type']['updated_at']);
        }

        $initialFeeTransaction = $installment->transactions()->firstWhere('is_initial_fee', true);
        $this->assertEquals($initialFeeTransaction->id, $response->json('data.singleInstallment.initial_fee_transaction.id'));
        $this->assertEquals($initialFeeTransaction->branch_id, $response->json('data.singleInstallment.initial_fee_transaction.branch_id'));
        $this->assertEquals($initialFeeTransaction->admin_id, $response->json('data.singleInstallment.initial_fee_transaction.admin_id'));
        $this->assertEquals($initialFeeTransaction->installment_id, $response->json('data.singleInstallment.initial_fee_transaction.installment_id'));
        $this->assertEquals(true, $response->json('data.singleInstallment.initial_fee_transaction.is_initial_fee'));
        $this->assertEquals($initialFeeTransaction->customer_id, $response->json('data.singleInstallment.initial_fee_transaction.customer_id'));
        $this->assertEquals($initialFeeTransaction->transaction_type_id, $response->json('data.singleInstallment.initial_fee_transaction.transaction_type_id'));
        $this->assertEquals($initialFeeTransaction->card_id, $response->json('data.singleInstallment.initial_fee_transaction.card_id'));
        $this->assertEquals($initialFeeTransaction->card_pan, $response->json('data.singleInstallment.initial_fee_transaction.card_pan'));
        $this->assertEquals($initialFeeTransaction->external_id, $response->json('data.singleInstallment.initial_fee_transaction.external_id'));
        $this->assertEquals($initialFeeTransaction->amount, $response->json('data.singleInstallment.initial_fee_transaction.amount'));
        $this->assertEquals($initialFeeTransaction->status->value, $response->json('data.singleInstallment.initial_fee_transaction.status'));
        $this->assertEquals($initialFeeTransaction->paid_at->timestamp, $response->json('data.singleInstallment.initial_fee_transaction.paid_at'));
        $this->assertEquals($initialFeeTransaction->created_at->timestamp, $response->json('data.singleInstallment.initial_fee_transaction.created_at'));
        $this->assertEquals($initialFeeTransaction->updated_at->timestamp, $response->json('data.singleInstallment.initial_fee_transaction.updated_at'));
    }

    private function assertCustomerFields(array $customerData): void
    {
        // customer
        $customer = Customer::query()->find(Arr::get($customerData, 'id'));
        $this->assertNotNull($customer);
        $this->assertEquals($customer->id, Arr::get($customerData, 'id'));
        $this->assertEquals($customer->pinfl, Arr::get($customerData, 'pinfl'));
        $this->assertEquals($customer->tin, Arr::get($customerData, 'tin'));
        $this->assertEquals($customer->passport_series, Arr::get($customerData, 'passport_series'));
        $this->assertEquals($customer->passport_number, Arr::get($customerData, 'passport_number'));
        $this->assertEquals($customer->passport_given_date?->format('d-m-Y'), Arr::get($customerData, 'passport_given_date'));
        $this->assertEquals($customer->passport_expiration_date?->format('d-m-Y'), Arr::get($customerData, 'passport_expiration_date'));
        $this->assertEquals($customer->passport_given_place, Arr::get($customerData, 'passport_given_place'));
        $this->assertEquals($customer->passport_citizenship, Arr::get($customerData, 'passport_citizenship'));
        $this->assertEquals($customer->first_name, Arr::get($customerData, 'first_name'));
        $this->assertEquals($customer->last_name, Arr::get($customerData, 'last_name'));
        $this->assertEquals($customer->middle_name, Arr::get($customerData, 'middle_name'));
        $this->assertEquals($customer->birth_date?->format('d-m-Y'), Arr::get($customerData, 'birth_date'));
        $this->assertEquals($customer->birth_place, Arr::get($customerData, 'birth_place'));
        $this->assertEquals($customer->gender->value, Arr::get($customerData, 'gender'));
        $this->assertEquals($customer->created_at->timestamp, Arr::get($customerData, 'created_at'));
        $this->assertEquals($customer->updated_at->timestamp, Arr::get($customerData, 'updated_at'));
        $this->assertEquals($customer->deleted_at, Arr::get($customerData, 'deleted_at'));

        // customer cards
        $this->assertEquals($customer->cards[0]->id, Arr::get($customerData, 'cards.0.id'));
        $this->assertEquals($customer->cards[0]->customer_id, Arr::get($customerData, 'cards.0.customer_id'));
        $this->assertEquals($customer->cards[0]->pan, Arr::get($customerData, 'cards.0.number'));
        $this->assertEquals($customer->cards[0]->card_type, Arr::get($customerData, 'cards.0.card_type'));
        $this->assertEquals($customer->cards[0]->owner, Arr::get($customerData, 'cards.0.owner'));
        $this->assertEquals($customer->cards[0]->expiry, Arr::get($customerData, 'cards.0.expiry'));
        $this->assertEquals($customer->cards[0]->is_connected, Arr::get($customerData, 'cards.0.is_connected'));
        $this->assertEquals($customer->cards[0]->phone, Arr::get($customerData, 'cards.0.phone'));
        $this->assertEquals($customer->cards[0]->status->value, Arr::get($customerData, 'cards.0.status'));
        $this->assertEquals($customer->cards[0]->balance, Arr::get($customerData, 'cards.0.balance'));
        $this->assertEquals($customer->cards[0]->created_at->timestamp, Arr::get($customerData, 'cards.0.created_at'));
        $this->assertEquals($customer->cards[0]->updated_at->timestamp, Arr::get($customerData, 'cards.0.updated_at'));

        // customer addresses
        $this->assertEquals($customer->addresses[0]->address, Arr::get($customerData, 'addresses.0.address'));
        $this->assertEquals($customer->addresses[0]->type->value, Arr::get($customerData, 'addresses.0.type'));
        $this->assertEquals($customer->addresses[0]->region_id, Arr::get($customerData, 'addresses.0.region_id'));
        $this->assertEquals($customer->addresses[0]->region->id, Arr::get($customerData, 'addresses.0.region.id'));
        $this->assertEquals($customer->addresses[0]->region->name, Arr::get($customerData, 'addresses.0.region.name'));
        $this->assertEquals($customer->addresses[0]->district_id, Arr::get($customerData, 'addresses.0.district_id'));
        $this->assertEquals($customer->addresses[0]->district->id, Arr::get($customerData, 'addresses.0.district.id'));
        $this->assertEquals($customer->addresses[0]->district->region_id, Arr::get($customerData, 'addresses.0.district.region_id'));
        $this->assertEquals($customer->addresses[0]->district->name, Arr::get($customerData, 'addresses.0.district.name'));
        $this->assertEquals($customer->addresses[0]->mahalla_id, Arr::get($customerData, 'addresses.0.mahalla_id'));
        $this->assertEquals($customer->addresses[0]->mahalla->district_id, Arr::get($customerData, 'addresses.0.mahalla.district_id'));
        $this->assertEquals($customer->addresses[0]->mahalla->name, Arr::get($customerData, 'addresses.0.mahalla.name'));
        $this->assertEquals($customer->addresses[0]->created_at->timestamp, Arr::get($customerData, 'addresses.0.updated_at'));
        $this->assertEquals($customer->addresses[0]->updated_at->timestamp, Arr::get($customerData, 'addresses.0.updated_at'));
        $this->assertEquals($customer->addresses[0]->deleted_at, Arr::get($customerData, 'addresses.0.deleted_at'));

        // customer phones
        $this->assertEquals($customer->phones[0]->id, Arr::get($customerData, 'phones.0.id'));
        $this->assertEquals($customer->phones[0]->phone, Arr::get($customerData, 'phones.0.phone'));
        $this->assertEquals($customer->phones[0]->pivot->type, Arr::get($customerData, 'phones.0.type'));
        $this->assertEquals($customer->phones[0]->pivot->comment, Arr::get($customerData, 'phones.0.comment'));
        $this->assertEquals($customer->phones[0]->pivot->created_at->timestamp, Arr::get($customerData, 'phones.0.created_at'));
        $this->assertEquals($customer->phones[0]->pivot->updated_at->timestamp, Arr::get($customerData, 'phones.0.updated_at'));
    }
}
