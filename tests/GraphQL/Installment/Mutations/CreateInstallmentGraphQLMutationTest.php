<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Tariff;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateInstallmentGraphQLMutationTest extends GraphQLTestCase
{
    private const string GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private Customer $_customer;
    private Tariff $_tariff;
    private Branch $_branch;

    protected function setUp(): void
    {
        parent::setUp();
        Customer::query()->forceDelete();
        Tariff::query()->forceDelete();
        Branch::query()->forceDelete();
        Installment::query()->forceDelete();
        CompanyAdmin::query()->forceDelete();

        $this->_customer = Customer::factory()->create();
        $this->_tariff = Tariff::factory()->create();
        $this->_branch = Branch::factory()->create();

        $this->_input = [
            'tariff_id' => $this->_tariff->id,
            'period' => rand($this->_tariff->min_period, $this->_tariff->max_period),
            'amount' => rand($this->_tariff->min_amount, $this->_tariff->max_amount),
            'initial_fee' => rand($this->_tariff->min_initial_fee, $this->_tariff->max_initial_fee),
            'description' => 'test',
            'starts_in' => '2022-01-01',
        ];
    }

    #[Test]
    public function successCreate(): void
    {
        $response = $this->sendGraphQLRequest($this->_customer->id, $this->_branch->id, $this->_input);
        $this->assertSuccessCreateInstallmentResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        $this->_input = array_merge($this->_input, [
            'tariff_id' => $this->_tariff->id,
            'period' => 12,
            'amount' => 1000,
            'initial_fee' => 100,
        ]);

        $response = $this->sendGraphQLRequest(9999, null, $this->_input);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertValidationErrors($response);
    }

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this->sendGraphQLRequest($this->_customer->id, $this->_branch->id, $this->_input);
        $response->assertOk();
        $this->assertEquals('Unauthorized', $response->json('errors.0.message'));

        tenant()->updateOrFail(['status' => CompanyStatusEnum::ACTIVE]);
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_CREATE)->firstOrFail();
        $branches = Branch::factory()->count(2)->create();

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($this->_customer->id, $branches[1]->id, $this->_input)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('validation.exists', ['attribute' => 'branch id']), $response->json('errors.0.extensions.validation.branch_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($this->_customer->id, $branches[1]->id, $this->_input)
            );
        $this->assertSuccessCreateInstallmentResponse($response);
    }

    private function sendGraphQLRequest(int $customer_id, ?int $branch_id, array $installment = []): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($customer_id, $branch_id, $installment)
            );
    }

    private function buildGraphQLQuery(int $customer_id, ?int $branch_id, $installment): array
    {
        if ($branch_id) {
            $branchVar = '$branch_id: Int!, ';
            $branchQuery = 'branch_id: $branch_id, ';
            $variables = ['customer_id' => $customer_id, 'branch_id' => $branch_id, 'installment' => $installment];
        } else {
            $branchVar = '';
            $branchQuery = '';
            $variables = ['customer_id' => $customer_id, 'installment' => $installment];
        }

        return [
            'variables' => $variables,
            'query' => '
            mutation ($customer_id: Int!, ' . $branchVar . '$installment: InstallmentInputGraphQLType!) {
                createInstallment(customer_id: $customer_id, ' . $branchQuery . 'installment: $installment) {
                    id
                    customer_id
                    customer {
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
                    }
                    cards {
                        id
                        customer_id
                        customer {
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
                        }
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
                    graphs  {
                        id
                        amount
                        paid_amount
                        overdue_days
                        status
                        starts_in
                    }
                    transactions {
                           id
                           branch_id
                           branch {
                                id
                                name
                                address
                                inn
                                created_at
                                updated_at
                                deleted_at
                           }
                           admin_id
                           admin {
                                id
                                username
                                first_name
                                last_name
                                phone
                                status
                                avatar {
                                    id
                                    type
                                    url
                                    name
                                    original_name
                                    mime
                                }
                                template_theme {
                                    layout_name
                                }
                                language
                                role_id
                                role {
                                    id
                                    name
                                    created_at
                                    updated_at
                                    deleted_at
                                    permission_groups {
                                        id
                                        slug
                                        name
                                        permissions {
                                            branches {
                                                id
                                                name
                                                editable
                                                enabled
                                            }
                                        }
                                    }
                                    permissions {
                                        profile_edit
                                        profile_change_language
                                        profile_change_avatar
                                    }
                                }
                                created_at
                                updated_at
                                deleted_at
                           }
                           installment_id
                           installment {
                                id
                                customer_id
                                customer {
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
                                }
                                tariff_id
                                tariff {
                                    id
                                    name
                                    percent
                                    min_period
                                    max_period
                                    min_amount
                                    max_amount
                                    min_initial_fee
                                    max_initial_fee
                                }
                                type
                                amount
                                initial_fee
                                description
                                sms_notify
                                is_blacklist
                                period
                                canceled_at
                                cancellation_details
                                starts_in
                                next_pay_date
                            }
                           customer_id
                           customer {
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
                                    customer {
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
                                    }
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
                                    district {
                                        id
                                        name
                                        mahallas {
                                            id
                                            name
                                        }
                                    }
                                    mahalla {
                                        id
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
                                    id
                                }
                           }
                           transaction_type_id
                           transaction_type {
                                id
                                is_system
                                name
                                name_en
                                name_ru
                                name_uz
                                created_at
                                updated_at
                                deleted_at
                            }
                           card_id
                           card {
                               id
                                customer_id
                                customer {
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
                                        customer {
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
                                        }
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
                                        district {
                                            id
                                            name
                                            mahallas {
                                                id
                                                name
                                            }
                                        }
                                        mahalla {
                                            id
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
                                        id
                                    }
                                }
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
                           external_id
                           amount
                           paid_at
                           created_at
                           updated_at
                    }
                    guarantors {
                        id
                        installment_id
                        installment {
                                id
                                customer_id
                                customer {
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
                                }
                                tariff_id
                                tariff {
                                    id
                                    name
                                    percent
                                    min_period
                                    max_period
                                    min_amount
                                    max_amount
                                    min_initial_fee
                                    max_initial_fee
                                }
                                type
                                amount
                                initial_fee
                                description
                                sms_notify
                                is_blacklist
                                period
                                canceled_at
                                cancellation_details
                                starts_in
                                next_pay_date
                            }
                        customer_id
                        customer {
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
                        }
                        any_card_charge
                        created_at
                        updated_at
                        deleted_at
                    }
                    comments {
                        id
                        installment_id
                        installment {
                            id
                            customer_id
                            customer {
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
                            }
                            tariff_id
                            tariff {
                                id
                                name
                                percent
                                min_period
                                max_period
                                min_amount
                                max_amount
                                min_initial_fee
                                max_initial_fee
                            }
                            type
                            amount
                            initial_fee
                            description
                            sms_notify
                            is_blacklist
                            period
                            canceled_at
                            cancellation_details
                            starts_in
                            next_pay_date
                        }
                        admin_id
                        admin {
                            id
                            username
                            first_name
                            last_name
                            phone
                            status
                            avatar {
                                id
                                type
                                url
                                name
                                original_name
                                mime
                            }
                            template_theme {
                                layout_name
                            }
                            language
                            role_id
                            role {
                                id
                                name
                                created_at
                                updated_at
                                deleted_at
                                permission_groups {
                                    id
                                    slug
                                    name
                                    permissions {
                                        branches {
                                            id
                                            name
                                            editable
                                            enabled
                                        }
                                    }
                                }
                                permissions {
                                    profile_edit
                                    profile_change_language
                                    profile_change_avatar
                                }
                            }
                            created_at
                            updated_at
                            deleted_at
                        }
                        comment
                        created_at
                        updated_at
                        deleted_at
                    }
                    documents {
                        id
                        type
                        url
                        name
                        original_name
                        mime
                        created_at
                        updated_at
                        deleted_at
                    }
                    status
                    branch_id
                    branch {
                        id
                        name
                        address
                        inn
                        created_at
                        updated_at
                        deleted_at
                    }
                    tariff_id
                    tariff {
                        id
                        name
                        percent
                        min_period
                        max_period
                        min_amount
                        max_amount
                        min_initial_fee
                        max_initial_fee
                    }
                    type
                    amount
                    initial_fee
                    sms_notify
                    auto_pay
                    auto_pay
                    auto_pay_by_pinfl
                    period
                    description
                    cancellation_details
                    starts_in
                    next_pay_date
                    created_at
                    updated_at
                    canceled_at
                    deleted_at
                }
            }
        ',
        ];
    }

    private function assertSuccessCreateInstallmentResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();

        $customer = [];
        $card = [];
        $installment = [];
        $graph = [
            'id',
            'amount',
            'paid_amount',
            'overdue_days',
            'status',
            'starts_in',
        ];
        $document = [
            'id',
            'type',
            'url',
            'name',
            'original_name',
            'mime',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        $admin = [
            'id',
            'username',
            'first_name',
            'last_name',
            'phone',
            'status',
            'avatar' => [
                'id',
                'type',
                'url',
                'name',
                'original_name',
                'mime',
            ],
            'template_theme' => [
                'layout_name',
            ],
            'language',
            'role_id',
            'role' => [
                'id',
                'name',
                'created_at',
                'updated_at',
                'deleted_at',
                'permission_groups' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'permissions' => [
                            'branches' => [
                                '*' => [
                                    'id',
                                    'name',
                                    'editable',
                                    'enabled',
                                ],
                            ],
                        ],
                    ],
                ],
                'permissions' => [
                    'profile_edit',
                    'profile_change_language',
                    'profile_change_avatar',
                ],
            ],
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        $comment = [
            'id',
            'installment_id',
            'installment' => $installment,
            'admin_id',
            'admin' => $admin,
            'comment',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        $branch = [
            'id',
            'name',
            'address',
            'inn',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        $transaction = [
            'id',
            'branch_id',
            'branch' => $branch,
            'admin_id',
            'admin' => $admin,
            'installment_id',
            'installment' => $installment,
            'customer_id',
            'customer' => $customer,
            'transaction_type_id',
            'transaction_type' => [
                'id',
                'is_system',
                'name',
                'name_en',
                'name_ru',
                'name_uz',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            'card_id',
            'card' => $card,
            'external_id',
            'amount',
            'paid_at',
            'created_at',
            'updated_at',
        ];
        $tariff = [
            'id',
            'name',
            'percent',
            'min_period',
            'max_period',
            'min_amount',
            'max_amount',
            'min_initial_fee',
            'max_initial_fee',
        ];
        $response->assertJsonStructure(
            ['data' => [
                'createInstallment' => [
                    'id',
                    'customer_id',
                    'customer' => $customer,
                    'cards' => [
                        '*' => $card,
                    ],
                    'graphs' => [
                        '*' => $graph,
                    ],
                    'transactions' => [
                        '*' => $transaction,
                    ],
                    'guarantors' => [
                        '*' => [
                            'id',
                            'installment_id',
                            'installment' => $installment,
                            'customer_id',
                            'customer' => $customer,
                            'any_card_charge',
                            'created_at',
                            'updated_at',
                            'deleted_at',
                        ],
                    ],
                    'comments' => ['*' => $comment],
                    'documents' => ['*' => $document],
                    'status',
                    'branch_id',
                    'branch' => $branch,
                    'tariff_id',
                    'tariff' => $tariff,
                    'type',
                    'amount',
                    'initial_fee',
                    'sms_notify',
                    'auto_pay',
                    'auto_pay',
                    'auto_pay_by_pinfl',
                    'period',
                    'description',
                    'cancellation_details',
                    'starts_in',
                    'next_pay_date',
                    'created_at',
                    'updated_at',
                    'canceled_at',
                    'deleted_at',
                ],
            ],
            ]);

    }

    private function assertValidationErrors(TestResponse $response): void
    {
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'customer id']),
            $response->json('errors.0.extensions.validation')['customer_id'][0]
        );
        $this->assertEquals(
            trans('validation.required', ['attribute' => 'branch id']),
            $response->json('errors.0.extensions.validation')['branch_id'][0]
        );
    }
}
