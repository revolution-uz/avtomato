<?php

namespace GraphQL\Management\Mutations;

use App\Models\Company\Tag;
use Illuminate\Support\Arr;
use Tests\GraphQL\GraphQLTestCase;

class UpdateTagGraphQlMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Tag $tag;

    private array $_input;

    public function setUp(): void
    {
        parent::setUp();
        // Create a tag to update
        $this->tag = Tag::factory()->create();
        $this->_input = [
            'name_uz' => 'new uz',
            'name_ru' => 'new ru',
            'name_en' => 'new en',
            'name_oz' => 'new oz',
        ];
        // Set headers for the test request
        $this->withHeaders(['Accept-Language' => 'uz']);
    }

    /**
     * @test
     */
    public function successWithoutArguments(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'),
                $this->buildGraphQLQuery(['tag' => $this->_input, 'tag_id' => $this->tag->id]));
        $response->assertOk();
        $response->assertJson([
            'data' => [
                'updateTag' => [
                    'id' => $this->tag->id,
                    'name_oz' => 'new oz',
                    'name_en' => 'new en',
                    'name_ru' => 'new ru',
                    'name_uz' => 'new uz',
                ],
            ],
        ]);
        $this->assertDatabaseHas('tags', [
            'id' => $this->tag->id,
            'name_oz' => 'new oz',
            'name_en' => 'new en',
            'name_ru' => 'new ru',
            'name_uz' => 'new uz',
        ]);
    }

    /**
     * @test
     */
    public function errorWithArguments(): void
    {
        // error tag_id
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'),
                $this->buildGraphQLQuery(['tag' => $this->_input, 'tag_id' => 123]));
        $errors = $response->json('errors.0');
        $response->assertOk();
        $this->assertEquals('validation', $errors['message']);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'tag id']), $errors['extensions']['validation']['tag_id'][0]);

        // error tags type
        $input = [
            'name_uz' => 1,
            'name_ru' => 1,
            'name_en' => 1,
            'name_oz' => 1,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'),
                $this->buildGraphQLQuery(['tag' => $input, 'tag_id' => $this->tag->id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 1 at "tag.name_uz"; String cannot represent a non string value: 1'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 1 at "tag.name_oz"; String cannot represent a non string value: 1'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 1 at "tag.name_en"; String cannot represent a non string value: 1'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tag" got invalid value 1 at "tag.name_ru"; String cannot represent a non string value: 1'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'variables' => $variables,
            'query' => '
            mutation UpdateTag($tag_id: Int!, $tag: TagInputGraphQLType!) {
                updateTag(tag_id: $tag_id, tag: $tag) {
                    id
                    name_oz
                    name_en
                    name_ru
                    name_uz
                }
            }
        ',
        ];
    }
}
