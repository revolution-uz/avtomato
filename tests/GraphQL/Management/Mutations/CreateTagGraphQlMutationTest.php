<?php

namespace GraphQL\Management\Mutations;

use App\Models\Company\Tag;
use Illuminate\Support\Arr;
use Tests\GraphQL\GraphQLTestCase;

class CreateTagGraphQlMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input;

    public function setUp(): void
    {
        parent::setUp();
        $this->_input = [
            'name_uz' => 'tag_uz',
            'name_ru' => 'tag_ru',
            'name_en' => 'tag_en',
            'name_oz' => 'tag_oz',
        ];
        $this->withHeaders(['Accept-Language' => 'uz']);
    }

    /**
     * @test
     */
    public function successCreateTeg(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'),
                $this->buildGraphQLQuery(['tag' => $this->_input]));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'createTag' => [
                    'id',
                    'name',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                ],
            ],
        ]);
        $data = $response->json('data.createTag');
        $this->assertIsInt($data['id']);
        $this->assertIsString($data['name']);
        $this->assertIsString($data['name_uz']);
        $this->assertIsString($data['name_ru']);
        $this->assertIsString($data['name_en']);
        $this->assertIsString($data['name_oz']);
        $tag = Tag::query()->findOrFail($data['id']);
        $this->assertEquals($data['name'], $tag->name);
        $this->assertEquals($data['name_uz'], $tag->name_uz);
        $this->assertEquals($data['name_ru'], $tag->name_ru);
        $this->assertEquals($data['name_en'], $tag->name_en);
        $this->assertEquals($data['name_oz'], $tag->name_oz);
    }

    /**
     * @test
     */
    public function argumentValidationErrors(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'), $this->buildGraphQLQuery(['tag' => [
                    'name_uz' => null,
                    'name_ru' => null,
                    'name_en' => null,
                    'name_oz' => null,
                ]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value null at "tag.name_uz"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value null at "tag.name_en"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value null at "tag.name_ru"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value null at "tag.name_oz"; Expected non-nullable type "String!" not to be null.'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'), $this->buildGraphQLQuery(['tag' => [
                    'name_uz' => 123,
                    'name_ru' => 123,
                    'name_en' => 123,
                    'name_oz' => 123,
                ]]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 123 at "tag.name_uz"; String cannot represent a non string value: 123'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 123 at "tag.name_oz"; String cannot represent a non string value: 123'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 123 at "tag.name_en"; String cannot represent a non string value: 123'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 123 at "tag.name_ru"; String cannot represent a non string value: 123'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation CreateTag($tag: TagInputGraphQLType!) {
                createTag(tag: $tag) {
                    id
                    name
                    name_uz
                    name_ru
                    name_en
                    name_oz
                }
            }
        ';

        return $query;
    }
}
