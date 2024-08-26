<?php

namespace Tests\GraphQL\Management\Mutations;

use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ChangeProfileGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private $_admin;

    private array $_input;

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
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_admin = $this->createAdminUser();
        $this->_input = [
            'first_name' => "{$this->_admin->first_name}-test",
            'last_name' => "{$this->_admin->last_name}-test",
        ];
    }

    #[Test]
    public function successChangeProfile()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessChangeProfile($response);
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $input = [];
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());

        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$first_name" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$last_name" of required type "String!" was not provided.'));

        $input = [
            'first_name' => 12324,
            'last_name' => 2314,
        ];
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$first_name\" got invalid value {$input['first_name']}; String cannot represent a non string value: {$input['first_name']}"));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$last_name\" got invalid value {$input['last_name']}; String cannot represent a non string value: {$input['last_name']}"));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation ChangeProfile ($first_name: String!, $last_name: String!) {
                changeProfile(first_name: $first_name, last_name: $last_name) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessChangeProfile(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'changeProfile' => $this->_structure,
            ],
        ]);

        $this->assertEquals($this->_admin->first_name, $this->_input['first_name']);
        $this->assertEquals($this->_admin->last_name, $this->_input['last_name']);

        $this->assertEquals($this->_admin->id, $response->json('data.changeProfile.id'));
        $this->assertEquals($this->_admin->username, $response->json('data.changeProfile.username'));
        $this->assertEquals($this->_admin->first_name, $response->json('data.changeProfile.first_name'));
        $this->assertEquals($this->_admin->last_name, $response->json('data.changeProfile.last_name'));
        $this->assertEquals($this->_admin->phone, $response->json('data.changeProfile.phone'));
        $this->assertEquals($this->_admin->status->value, $response->json('data.changeProfile.status'));
        $this->assertEquals($this->_admin->language, $response->json('data.changeProfile.language'));
        $this->assertEquals($this->_admin->role_id, $response->json('data.changeProfile.role_id'));
        $this->assertEquals($this->_admin->created_at->timestamp, $response->json('data.changeProfile.created_at'));
        $this->assertEquals($this->_admin->updated_at->timestamp, $response->json('data.changeProfile.updated_at'));
        $this->assertNull($response->json('data.changeProfile.deleted_at'));

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
