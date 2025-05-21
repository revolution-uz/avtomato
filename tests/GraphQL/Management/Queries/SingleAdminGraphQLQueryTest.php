<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\Attachment;
use App\Models\Company\CompanyAdmin;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleAdminGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_admin;

    private array $_structure = [
        'id',
        'username',
        'first_name',
        'last_name',
        'phone',
        'status',
        'language',
        'role_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'avatar' => [
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
        'template_theme' => [
            'layout_name',
        ],
        'role' => [
            'id',
            'name',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        CompanyAdmin::query()->forceDelete();
        $this->_admin = $this->createAdminUser();
        CompanyAdmin::factory()->count(30)->create();
        foreach (CompanyAdmin::all() as $admin) {
            Attachment::factory()->create([
                'attachmentable_id' => $admin->id,
                'attachmentable_type' => CompanyAdmin::class,
            ]);
        }
    }

    #[Test]
    public function successWithId()
    {
        $admin = CompanyAdmin::query()->first();

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $admin->id])
            );

        $this->assertSuccessSingleAdminResponse($response, $admin);
    }

    #[Test]
    public function errorInvalidId()
    {
        $id = CompanyAdmin::query()->orderByDesc('id')->first()->id;
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $id + 1])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
    }

    #[Test]
    public function errorValidationId()
    {
        $id = 'hjebfwef';
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$id\" got invalid value \"{$id}\"; Int cannot represent non-integer value: \"{$id}\"", $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query SingleAdmin($id: Int!) {
                singleAdmin(id: $id) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessSingleAdminResponse(TestResponse $response, CompanyAdmin $admin): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleAdmin' => $this->_structure,
            ],
        ]);

        $this->assertEquals($admin->id, $response->json('data.singleAdmin.id'));
        $this->assertEquals($admin->username, $response->json('data.singleAdmin.username'));
        $this->assertEquals($admin->first_name, $response->json('data.singleAdmin.first_name'));
        $this->assertEquals($admin->last_name, $response->json('data.singleAdmin.last_name'));
        $this->assertEquals($admin->phone, $response->json('data.singleAdmin.phone'));
        $this->assertEquals($admin->status->value, $response->json('data.singleAdmin.status'));
        $this->assertEquals($admin->language, $response->json('data.singleAdmin.language'));
        $this->assertEquals($admin->role_id, $response->json('data.singleAdmin.role_id'));
        $this->assertEquals($admin->created_at->timestamp, $response->json('data.singleAdmin.created_at'));
        $this->assertEquals($admin->updated_at?->timestamp, $response->json('data.singleAdmin.updated_at'));
        $this->assertEquals($admin->deleted_at?->timestamp, $response->json('data.singleAdmin.deleted_at'));

        $this->assertEquals($admin->avatar->id, $response->json('data.singleAdmin.avatar.id'));
        $this->assertEquals($admin->avatar->type->value, $response->json('data.singleAdmin.avatar.type'));
        $this->assertEquals($admin->avatar->url, $response->json('data.singleAdmin.avatar.url'));
        $this->assertEquals($admin->avatar->name, $response->json('data.singleAdmin.avatar.name'));
        $this->assertEquals($admin->avatar->original_name, $response->json('data.singleAdmin.avatar.original_name'));
        $this->assertEquals($admin->avatar->mime, $response->json('data.singleAdmin.avatar.mime'));
        $this->assertEquals($admin->avatar->created_at->timestamp, $response->json('data.singleAdmin.avatar.created_at'));
        $this->assertEquals($admin->avatar->updated_at?->timestamp, $response->json('data.singleAdmin.avatar.updated_at'));
        $this->assertEquals($admin->avatar->deleted_at?->timestamp, $response->json('data.singleAdmin.avatar.deleted_at'));

        $this->assertEquals($admin->template_theme->layout_name, $response->json('data.singleAdmin.template_theme.layout_name'));

        $this->assertEquals($admin->role->id, $response->json('data.singleAdmin.role.id'));
        $this->assertEquals($admin->role->name, $response->json('data.singleAdmin.role.name'));
        $this->assertEquals($admin->role->created_at->timestamp, $response->json('data.singleAdmin.role.created_at'));
        $this->assertEquals($admin->role->updated_at?->timestamp, $response->json('data.singleAdmin.role.updated_at'));
        $this->assertEquals($admin->role->deleted_at?->timestamp, $response->json('data.singleAdmin.role.deleted_at'));
    }
}
