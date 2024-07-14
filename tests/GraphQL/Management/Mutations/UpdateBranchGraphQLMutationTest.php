<?php

namespace Management\Mutations;

use App\Models\Company\Branch;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateBranchGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Branch::factory()->count(30)->create();
    }

    #[Test]
    public function successUpdateBranch()
    {
        $branch = Branch::query()->inRandomOrder()->first();

        $input = [
            'id' => $branch->id,
            'name' => Str::random(255),
            'address' => Str::random(255),
            'inn' => Str::random(9),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateBranch' => [
                    'id',
                    'name',
                    'address',
                    'inn',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $branch->refresh();
        $this->assertEquals($branch->name, $input['name']);
        $this->assertEquals($branch->address, $input['address']);
        $this->assertEquals($branch->inn, $input['inn']);

        $this->assertEquals($branch->id, $response->json('data.updateBranch.id'));
        $this->assertEquals($branch->name, $response->json('data.updateBranch.name'));
        $this->assertEquals($branch->address, $response->json('data.updateBranch.address'));
        $this->assertEquals($branch->inn, $response->json('data.updateBranch.inn'));
        $this->assertEquals($branch->created_at->timestamp, $response->json('data.updateBranch.created_at'));
        $this->assertEquals($branch->updated_at->timestamp, $response->json('data.updateBranch.updated_at'));
        $this->assertNull($response->json('data.updateBranch.deleted_at'));
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $input = [
            'id' => null,
            'name' => null,
            'address' => null,
            'inn' => null,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));
        $this->assertEquals('Variable "$name" of non-null type "String!" must not be null.', $response->json('errors.1.message'));
        $this->assertEquals('Variable "$address" of non-null type "String!" must not be null.', $response->json('errors.2.message'));
        $this->assertEquals('Variable "$inn" of non-null type "String!" must not be null.', $response->json('errors.3.message'));

        $input = [
            'id' => Branch::query()->orderByDesc('id')->first()->id + 1,
            'name' => Str::random(256),
            'address' => Str::random(256),
            'inn' => Str::random(10),
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
                            'name',
                            'address',
                            'inn',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'Имя', 'max' => 255]), $response->json('errors.0.extensions.validation.name.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'address', 'max' => 255]), $response->json('errors.0.extensions.validation.address.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'inn', 'max' => 9]), $response->json('errors.0.extensions.validation.inn.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateBranch($id: Int!, $name: String!, $address: String!, $inn: String!) {
                updateBranch(id: $id, name: $name, address: $address, inn: $inn) {
                    id
                    name
                    address
                    inn
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ';

        return $query;
    }
}
