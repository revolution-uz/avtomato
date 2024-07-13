<?php

namespace Management\Mutations;

use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ChangeProfileLanguageGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private $_admin;

    private $_languageInput = 'en';

    protected function setUp(): void
    {
        parent::setUp();

        $this->_admin = CompanyAdmin::factory()->create([
            'role_id' => Role::getAdmin()->id,
            'language' => 'ru',
        ]);
    }

    #[Test]
    public function successChangeProfileLanguage()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_languageInput)
            );

        $this->assertSuccessChangeProfileLanguage($response);
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $language = null;
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($language)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$language" of non-null type "String!" must not be null.'));

        $language = 3489;
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($language)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$language\" got invalid value {$language}; String cannot represent a non string value: {$language}"));

        $language = 'tj';
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($language)
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'language',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.in', ['attribute' => 'language']), $response->json('errors.0.extensions.validation.language.0'));
    }

    private function buildGraphQLQuery(mixed $language = null): array
    {
        $query = [];
        $query['variables'] = [
            'language' => $language,
        ];
        $query['query'] = '
            mutation ChangeProfileLanguage ($language: String!) {
                changeProfileLanguage(language: $language)
            }
        ';

        return $query;
    }

    private function assertSuccessChangeProfileLanguage(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'changeProfileLanguage',
            ],
        ]);

        $this->assertEquals($this->_admin->language, $this->_languageInput);
        $this->assertEquals($this->_admin->language, $response->json('data.changeProfileLanguage'));
    }
}
