<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateTransactionTypeGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('tenants:seed', ['--class' => TransactionTypeSeeder::class, '--tenants' => [tenant()->id]]);
        TransactionType::factory()->count(5)->create();
    }

    #[Test]
    public function successUpdateTransactionType()
    {
        $transactionType = TransactionType::query()->where('id', '>=', 100)->inRandomOrder()->first();

        $input = [
            'id' => $transactionType->id,
            'name' => Str::random(32),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateTransactionType' => [
                    'id',
                    'name',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'is_system',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $transactionType->refresh();
        $this->assertEquals($transactionType->name, $input['name']);
        $this->assertEquals($transactionType->name_uz, $input['name']);
        $this->assertEquals($transactionType->name_ru, $input['name']);
        $this->assertEquals($transactionType->name_en, $input['name']);

        $this->assertEquals($transactionType->id, $response->json('data.updateTransactionType.id'));
        $this->assertEquals($transactionType->name, $response->json('data.updateTransactionType.name'));
        $this->assertEquals($transactionType->name_uz, $response->json('data.updateTransactionType.name_uz'));
        $this->assertEquals($transactionType->name_ru, $response->json('data.updateTransactionType.name_ru'));
        $this->assertEquals($transactionType->name_en, $response->json('data.updateTransactionType.name_en'));
        $this->assertEquals($transactionType->is_system, $response->json('data.updateTransactionType.is_system'));
        $this->assertEquals($transactionType->created_at->timestamp, $response->json('data.updateTransactionType.created_at'));
        $this->assertEquals($transactionType->updated_at->timestamp, $response->json('data.updateTransactionType.updated_at'));
        $this->assertNull($response->json('data.updateTransactionType.deleted_at'));
    }

    #[Test]
    public function errorUpdateSystemTransactionTypes(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => TransactionType::query()->where('id', '<', 100)->inRandomOrder()->first()->id,
                    'name' => Str::random(32),
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('You can not edit system transaction types', $response->json('errors.0.extensions.validation.id.0'));
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
                    'id' => null,
                    'name' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name" of non-null type "String!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => 999999999,
                    'name' => Str::random(33),
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'Имя', 'max' => 32]), $response->json('errors.0.extensions.validation.name.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateTransactionType($id: Int!, $name: String!) {
                updateTransactionType(id: $id, name: $name) {
                    id
                    name
                    name_uz
                    name_ru
                    name_en
                    is_system
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ';

        return $query;
    }
}
