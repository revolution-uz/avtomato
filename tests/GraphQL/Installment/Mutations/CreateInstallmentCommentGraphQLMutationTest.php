<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentComment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateInstallmentCommentGraphQLMutationTest extends GraphQLTestCase
{
    private const GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private string $_comment = 'This is a comment';
    private $_branch;

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        InstallmentComment::query()->forceDelete();
        $this->_branch = Branch::query()->firstOrFail();
    }

    #[Test]
    public function successComment()
    {
        $installment = Installment::factory()->create(['branch_id' => $this->_branch->id]);
        $response = $this->sendGraphQLRequest(
            installment_id: $installment->id,
            comment: $this->_comment,
        );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(
            [
                'data' => [
                    'createInstallmentComment' => [
                        'id',
                        'installment_id',
                        'installment' => [
                            'id',
                        ],
                        'admin_id',
                        'admin' => [
                            'id',
                        ],
                        'comment',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ]
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
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_CREATE_COMMENT)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment->id, $this->_comment)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment->id, $this->_comment)
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(
            [
                'data' => [
                    'createInstallmentComment' => [
                        'id',
                        'installment_id',
                        'installment' => [
                            'id',
                        ],
                        'admin_id',
                        'admin' => [
                            'id',
                        ],
                        'comment',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ]
        );
    }

    private function sendGraphQLRequest(int $installment_id, string $comment): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $comment)
            );
    }

    private function buildGraphQLQuery(int $installment_id, string $comment): array
    {
        return [
            'variables' => ['installment_id' => $installment_id, 'comment' => $comment],
            'query' => '
            mutation ($installment_id: Int!, $comment: String!) {
                createInstallmentComment(installment_id: $installment_id, comment: $comment){
                    id
                    installment_id
                    installment {
                        id
                    }
                    admin_id
                    admin {
                        id
                    }
                    comment
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ',
        ];
    }
}
