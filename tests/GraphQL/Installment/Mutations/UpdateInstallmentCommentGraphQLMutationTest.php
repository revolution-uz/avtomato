<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentComment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateInstallmentCommentGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private InstallmentComment $_installmentComment;

    protected function setUp(): void
    {
        parent::setUp();

        $branch = Branch::query()->firstOrFail();
        $installment = Installment::factory()->create(['branch_id' => $branch->id]);
        InstallmentComment::factory()->count(10)->create(['installment_id' => $installment->id]);
    }

    #[Test]
    public function successUpdateInstallmentComment()
    {
        $this->_installmentComment = InstallmentComment::query()->inRandomOrder()->first();
        $input = [
            'installment_id' => $this->_installmentComment->installment_id,
            'comment_id' => $this->_installmentComment->id,
            'comment' => Str::random(10),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input)
            );

        $this->assertSuccessResponse($response, $input);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => null,
                    'comment_id' => null,
                    'comment' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$comment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$comment" of non-null type "String!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => 999999999,
                    'comment_id' => 999999999,
                    'comment' => Str::random(10),
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'comment id']), $response->json('errors.0.extensions.validation.comment_id.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_UPDATE_COMMENT)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        $this->_installmentComment = InstallmentComment::factory()->create(['installment_id' => $installment->id]);
        $input = [
            'installment_id' => $installment->id,
            'comment_id' => $this->_installmentComment->id,
            'comment' => Str::random(10),
        ];

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($input));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($input));
        $this->assertSuccessResponse($response, $input);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateInstallmentComment(
                $installment_id: Int!
                $comment_id: Int!
                $comment: String!
            ) {
                updateInstallmentComment(
                    installment_id: $installment_id
                    comment_id: $comment_id
                    comment: $comment
                ) {
                    id
                    installment_id
                    admin_id
                    comment
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response, array $input): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateInstallmentComment' => [
                    'id',
                    'installment_id',
                    'admin_id',
                    'comment',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $this->_installmentComment->refresh();
        $this->assertEquals($this->_installmentComment->comment, $input['comment']);

        $this->assertEquals($this->_installmentComment->id, $response->json('data.updateInstallmentComment.id'));
        $this->assertEquals($this->_installmentComment->installment_id, $response->json('data.updateInstallmentComment.installment_id'));
        $this->assertEquals($this->_installmentComment->admin_id, $response->json('data.updateInstallmentComment.admin_id'));
        $this->assertEquals($this->_installmentComment->comment, $response->json('data.updateInstallmentComment.comment'));
        $this->assertEquals($this->_installmentComment->created_at->timestamp, $response->json('data.updateInstallmentComment.created_at'));
        $this->assertEquals($this->_installmentComment->updated_at->timestamp, $response->json('data.updateInstallmentComment.updated_at'));
        $this->assertNull($response->json('data.updateInstallmentComment.deleted_at'));
    }
}
