<?php

namespace Management\Mutations;

use App\Models\Company\CompanyAdmin;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteAdminGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_actingAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_actingAdmin = $this->createAdminUser();
        CompanyAdmin::factory()->count(5)->create();
    }

    #[Test]
    public function successDeleteAdmin()
    {
        $idsInput = CompanyAdmin::query()->whereNot('id', $this->_actingAdmin->id)->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->_actingAdmin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteAdmin',
            ],
        ]);

        $this->assertTrue($response->json('data.deleteAdmin'));
        $this->assertTrue(CompanyAdmin::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $idsInput = [null];
        $response = $this
            ->actingAs($this->_actingAdmin)
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
            ->actingAs($this->_actingAdmin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$ids\" got invalid value \"{$idsInput[0]}\" at \"ids[0]\"; Int cannot represent non-integer value: \"{$idsInput[0]}\""));

        $idsInput = [CompanyAdmin::query()->orderByDesc('id')->first()->id + 1];
        $response = $this
            ->actingAs($this->_actingAdmin)
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

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DeleteAdmin($ids: [Int]!) {
                deleteAdmin(ids: $ids)
            }
        ';

        return $query;
    }
}
