<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\BalanceFund;
use App\Models\Company\Feature;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class BalanceFillConfirmGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input = ['transaction_id' => 1, 'otp' => '111111', 'number' => 8600332914249390];

    protected function setUp(): void
    {
        parent::setUp();

        BalanceFund::query()->forceDelete();

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
    public function successBalanceFillConfirm()
    {
        AtmosFaker::confirmPayment();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorBalanceFillConfirmWithWrongOtp()
    {
        AtmosFaker::confirmPayment(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('OTP код указан неверно', $response->json('errors.0.extensions.validation.otp.0'));
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['transaction_id' => null, 'otp' => null, 'number' => null])
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$transaction_id" of non-null type "BigInteger!" must not be null.', $response->json('errors.0.message'));
        $this->assertEquals('Variable "$otp" of non-null type "String!" must not be null.', $response->json('errors.1.message'));
        $this->assertEquals('Variable "$number" of non-null type "BigInteger!" must not be null.', $response->json('errors.2.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(array_merge($this->_input, ['number' => '87678232']))
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'number', 'digits' => 16]), $response->json('errors.0.extensions.validation.number.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        AtmosFaker::confirmPayment();

        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::BALANCE_FUND_CONFIRM)->firstOrFail();

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
            mutation BalanceFillConfirm($transaction_id: BigInteger!, $otp: String!, $number: BigInteger!) {
                balanceFillConfirm(transaction_id: $transaction_id, otp: $otp, number: $number) {
                    id
                    admin_id
                    amount
                    phone_number
                    external_transaction_id
                    card_pan
                    created_at
                }
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'balanceFillConfirm' => [
                    'id',
                    'admin_id',
                    'phone_number',
                    'external_transaction_id',
                    'card_pan',
                    'created_at',
                ],
            ],
        ]);

        $balanceFund = BalanceFund::query()->firstOrFail();
        $this->assertEquals(2000, $balanceFund->amount);
        $this->assertEquals($this->_input['number'], $balanceFund->card_number);

        $this->assertEquals($balanceFund->id, $response->json('data.balanceFillConfirm.id'));
        $this->assertEquals($balanceFund->admin_id, $response->json('data.balanceFillConfirm.admin_id'));
        $this->assertEquals($balanceFund->amount, $response->json('data.balanceFillConfirm.amount'));
        $this->assertEquals($balanceFund->phone_number, $response->json('data.balanceFillConfirm.phone_number'));
        $this->assertEquals($balanceFund->external_transaction_id, $response->json('data.balanceFillConfirm.external_transaction_id'));
        $this->assertEquals($balanceFund->card_pan, $response->json('data.balanceFillConfirm.card_pan'));
        $this->assertEquals($balanceFund->created_at->timestamp, $response->json('data.balanceFillConfirm.created_at'));
    }
}
