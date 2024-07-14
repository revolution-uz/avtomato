<?php

namespace Management\Mutations;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ChangeProfileThemeGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private $_admin;

    private string $_layoutNameInput;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_admin = $this->createAdminUser();
        $this->_layoutNameInput = Str::random(10);
    }

    #[Test]
    public function successChangeProfileTheme()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'layout_name' => $this->_layoutNameInput,
                ])
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'changeProfileTheme' => [
                    'layout_name',
                ],
            ],
        ]);
        $this->assertEquals($this->_admin->template_theme->layout_name, $response->json('data.changeProfileTheme.layout_name'));
        $this->assertEquals($this->_layoutNameInput, $this->_admin->template_theme->layout_name);
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'layout_name' => null,
                ])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$layout_name" of non-null type "String!" must not be null.'));

        $layoutName = 1934;
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'layout_name' => $layoutName,
                ])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$layout_name\" got invalid value {$layoutName}; String cannot represent a non string value: {$layoutName}", $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation ChangeProfileTheme ($layout_name: String!) {
                changeProfileTheme(layout_name: $layout_name) {
                    layout_name
                }
            }
        ';

        return $query;
    }
}
