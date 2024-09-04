<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteRoleGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Role::factory()->count(5)->create();
        foreach (Role::query()->inRandomOrder()->limit(3)->pluck('id') as $role) {
            CompanyAdmin::factory()->create([
                'role_id' => $role,
            ]);
        }
    }

    #[Test]
    public function successDeleteRole()
    {
        $idsInput = Role::query()->whereNot('id', Role::getAdmin()->id)->doesntHave('admins')->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteRole',
            ],
        ]);

        $this->assertTrue($response->json('data.deleteRole'));
        $this->assertTrue(Role::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorDeletingAdminRole()
    {
        $idsInput = [Role::getAdmin()->id];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.You cannot delete', ['item' => Role::getAdmin()->name]), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $idsInput = [null];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);

        $idsInput = ['elkfmerk'];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$ids\" got invalid value \"{$idsInput[0]}\" at \"ids[0]\"; Int cannot represent non-integer value: \"{$idsInput[0]}\""));

        $idsInput = [Role::query()->orderByDesc('id')->first()->id + 1];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.not found', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function errorWhenAdminsExist()
    {
        $idsInput = Role::query()->whereNot('id', Role::getAdmin()->id)->has('admins')->inRandomOrder()->limit(1)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.are used', ['item' => __('api.Role'), 'element' => __('api.User')]), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function errorDeleteRoleWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $idsInput = Role::query()->whereNot('id', Role::getAdmin()->id)->doesntHave('admins')->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery(['ids' => $idsInput]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DeleteRole($ids: [Int]!) {
                deleteRole(ids: $ids)
            }
        ';

        return $query;
    }
}
