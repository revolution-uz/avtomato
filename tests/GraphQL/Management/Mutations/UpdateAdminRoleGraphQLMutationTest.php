<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateAdminRoleGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_adminInput;

    private Role $_roleInput;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_adminInput = CompanyAdmin::factory()->create();
        $this->_roleInput = Role::factory()->create();
    }

    #[Test]
    public function successUpdateAdminRole()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'id' => $this->_adminInput->id,
                    'role_id' => $this->_roleInput->id,
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateAdminRole',
            ],
        ]);
        $this->_adminInput->refresh();
        $this->assertTrue($response->json('data.updateAdminRole'));
        $this->assertEquals($this->_adminInput->role_id, $this->_roleInput->id);
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $input = [
            'id' => null,
            'role_id' => null,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));
        $this->assertEquals('Variable "$role_id" of non-null type "Int!" must not be null.', $response->json('errors.1.message'));

        $input = [
            'id' => CompanyAdmin::query()->orderByDesc('id')->first()->id + 1,
            'role_id' => Role::query()->orderByDesc('id')->first()->id + 1,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'id',
                            'role_id',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'role id']), $response->json('errors.0.extensions.validation.role_id.0'));
    }

    #[Test]
    public function errorUpdateAdminRoleWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $this->_adminInput->id, 'role_id' => $this->_roleInput->id])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
                mutation UpdateAdminRole($id: Int!, $role_id: Int!) {
                    updateAdminRole(id: $id, role_id: $role_id)
                }
        ';

        return $query;
    }
}
