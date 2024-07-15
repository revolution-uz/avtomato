<?php

namespace Management\Mutations;

use App\Models\Company\CompanyAdmin;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RevokeAdminDevicesGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_adminInput;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_adminInput = CompanyAdmin::factory()->create();
        PersonalAccessToken::factory()->count(5)->create([
            'tokenable_id' => $this->_adminInput->id,
            'tokenable_type' => get_class($this->_adminInput),
        ]);
    }

    #[Test]
    public function successRevokeAdminDevices()
    {
        $idsInput = PersonalAccessToken::query()
            ->where('tokenable_id', $this->_adminInput->id)->where('tokenable_type', get_class($this->_adminInput))
            ->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'admin_id' => $this->_adminInput->id,
                    'ids' => $idsInput,
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'revokeAdminDevices',
            ],
        ]);
        $this->assertTrue($response->json('data.revokeAdminDevices'));
        $this->assertTrue(PersonalAccessToken::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorWithoutAdminId()
    {
        $idsInput = PersonalAccessToken::query()
            ->where('tokenable_id', $this->_adminInput->id)->where('tokenable_type', get_class($this->_adminInput))
            ->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'admin_id' => null,
                    'ids' => $idsInput,
                ])
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$admin_id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'admin_id' => CompanyAdmin::factory()->create()->id,
                    'ids' => $idsInput,
                ])
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'revokeAdminDevices',
            ],
        ]);
        $this->assertTrue($response->json('data.revokeAdminDevices'));
        $this->assertTrue(PersonalAccessToken::query()->whereIn('id', $idsInput)->count() === count($idsInput));
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $idsInput = [null];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'admin_id' => $this->_adminInput->id,
                    'ids' => $idsInput,
                ])
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
        $this->assertEquals(trans('validation.integer', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);

        $idsInput = ['elkfmerk'];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'admin_id' => $this->_adminInput->id,
                    'ids' => $idsInput,
                ])
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$ids\" got invalid value \"{$idsInput[0]}\" at \"ids[0]\"; Int cannot represent non-integer value: \"{$idsInput[0]}\""));

        $idsInput = [PersonalAccessToken::query()->orderByDesc('id')->first()->id + 1];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'admin_id' => $this->_adminInput->id,
                    'ids' => $idsInput,
                ])
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
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation RevokeAdminDevices($admin_id: Int!, $ids: [Int]!) {
                revokeAdminDevices(admin_id: $admin_id, ids: $ids)
            }
        ';

        return $query;
    }
}
