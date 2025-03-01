<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Card;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AttachCardGraphQLMutationTest extends GraphQLTestCase
{
    private const string GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private Card $_card;
    private Installment $_installment;

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        Card::query()->forceDelete();
        $branch = Branch::query()->firstOrFail();

        $this->_card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE]);
        $this->_installment = Installment::factory()->create(['branch_id' => $branch->id]);
    }

    #[Test]
    public function successAttach(): void
    {
        $response = $this->sendGraphQLRequest(
            installment_id: $this->_installment->id,
            card_id: $this->_card->id,
        );

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorDuplicateAttachCard()
    {
        $this->successAttach();
        $response = $this->sendGraphQLRequest(
            installment_id: $this->_installment->id,
            card_id: $this->_card->id,
        );

        $response->assertJsonIsObject();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.unique', ['attribute' => 'card id']),
            $response->json('errors.0.extensions.validation')['card_id'][0]
        );
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this->sendGraphQLRequest(9999, 6556);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'installment id']),
            $response->json('errors.0.extensions.validation')['installment_id'][0]
        );
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'card id']),
            $response->json('errors.0.extensions.validation')['card_id'][0]
        );
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_ATTACH_CARD)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($installment->id, $this->_card->id));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($installment->id, $this->_card->id));
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorAttachCardWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this->sendGraphQLRequest(installment_id: $this->_installment->id, card_id: $this->_card->id);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function sendGraphQLRequest(int $installment_id, ?int $card_id): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $card_id)
            );
    }

    private function buildGraphQLQuery(int $installment_id, ?int $card_id): array
    {
        return [
            'variables' => ['installment_id' => $installment_id, 'card_id' => $card_id],
            'query' => '
            mutation ($installment_id: Int!, $card_id: Int!) {
                attachCard(installment_id: $installment_id, card_id: $card_id)
            }
        ',
        ];
    }

    private function assertSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();

        $response->assertJsonStructure(
            [
                'data' => [
                    'attachCard',
                ],
            ]
        );
    }
}
