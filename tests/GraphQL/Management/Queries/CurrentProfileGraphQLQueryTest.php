<?php

namespace Management\Queries;

use App\Models\Company\Attachment;
use App\Models\Company\CompanyAdmin;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CurrentProfileGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

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

    private CompanyAdmin $_admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_admin = $this->createAdminUser();
        Attachment::factory()->create([
            'attachmentable_id' => $this->_admin->id,
            'attachmentable_type' => get_class($this->_admin),
        ]);
    }

    #[Test]
    public function successCurrentProfile()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery()
            );
        $this->assertSuccessCurrentProfileResponse($response);
    }

    #[Test]
    public function errorUnauthorized()
    {
        $response = $this
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery()
            );
        $response->assertUnauthorized();
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('Unauthenticated.', $response->json('message'));
    }

    private function buildGraphQLQuery(): array
    {
        $query = [];
        $query['variables'] = [];
        $query['query'] = '
            query CurrentProfile {
                currentProfile {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessCurrentProfileResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'currentProfile' => $this->_structure,
            ],
        ]);

        $this->assertEquals($this->_admin->id, $response->json('data.currentProfile.id'));
        $this->assertEquals($this->_admin->username, $response->json('data.currentProfile.username'));
        $this->assertEquals($this->_admin->first_name, $response->json('data.currentProfile.first_name'));
        $this->assertEquals($this->_admin->last_name, $response->json('data.currentProfile.last_name'));
        $this->assertEquals($this->_admin->phone, $response->json('data.currentProfile.phone'));
        $this->assertEquals($this->_admin->status->value, $response->json('data.currentProfile.status'));
        $this->assertEquals($this->_admin->language, $response->json('data.currentProfile.language'));
        $this->assertEquals($this->_admin->role_id, $response->json('data.currentProfile.role_id'));
        $this->assertEquals($this->_admin->created_at->timestamp, $response->json('data.currentProfile.created_at'));
        $this->assertEquals($this->_admin->updated_at?->timestamp, $response->json('data.currentProfile.updated_at'));
        $this->assertEquals($this->_admin->deleted_at?->timestamp, $response->json('data.currentProfile.deleted_at'));

        $this->assertEquals($this->_admin->avatar->id, $response->json('data.currentProfile.avatar.id'));
        $this->assertEquals($this->_admin->avatar->type->value, $response->json('data.currentProfile.avatar.type'));
        $this->assertEquals($this->_admin->avatar->url, $response->json('data.currentProfile.avatar.url'));
        $this->assertEquals($this->_admin->avatar->name, $response->json('data.currentProfile.avatar.name'));
        $this->assertEquals($this->_admin->avatar->original_name, $response->json('data.currentProfile.avatar.original_name'));
        $this->assertEquals($this->_admin->avatar->mime, $response->json('data.currentProfile.avatar.mime'));
        $this->assertEquals($this->_admin->avatar->created_at->timestamp, $response->json('data.currentProfile.avatar.created_at'));
        $this->assertEquals($this->_admin->avatar->updated_at?->timestamp, $response->json('data.currentProfile.avatar.updated_at'));
        $this->assertEquals($this->_admin->avatar->deleted_at?->timestamp, $response->json('data.currentProfile.avatar.deleted_at'));

        $this->assertEquals($this->_admin->template_theme->layout_name, $response->json('data.currentProfile.template_theme.layout_name'));

        $this->assertEquals($this->_admin->role->id, $response->json('data.currentProfile.role.id'));
        $this->assertEquals($this->_admin->role->name, $response->json('data.currentProfile.role.name'));
        $this->assertEquals($this->_admin->role->created_at->timestamp, $response->json('data.currentProfile.role.created_at'));
        $this->assertEquals($this->_admin->role->updated_at?->timestamp, $response->json('data.currentProfile.role.updated_at'));
        $this->assertEquals($this->_admin->role->deleted_at?->timestamp, $response->json('data.currentProfile.role.deleted_at'));
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
