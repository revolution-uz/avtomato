<?php

namespace Tests\GraphQL\Scoring\Queries;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RoyxatCreditsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private $_structure = [
        'id',
        'credit_id',
        'amount',
        'duration',
        'name',
        'passport',
        'tin',
        'pinfl',
        'credit_status',
        'contracted_date',
        'overdue_days',
        'merchant_id',
        'merchant_name',
        'created_at',
        'updated_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockUrl('https://service.royxat.uz/api/credits/search*', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/royxat-credits.json')), true));
    }

    #[Test]
    public function successRoyxatCreditsWithPassport()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['passport' => 'AB1234567'])
            );

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function successRoyxatCreditsWithPINFL()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['pinfl' => '12345678910234'])
            );

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function successRoyxatCreditsWithTIN()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['tin' => '123456789'])
            );

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function validationError()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery()
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required_without_all', ['attribute' => 'passport', 'values' => 'pinfl / tin']), $response->json('errors.0.extensions.validation.passport.0'));
        $this->assertEquals(trans('validation.required_without_all', ['attribute' => 'pinfl', 'values' => 'passport / tin']), $response->json('errors.0.extensions.validation.pinfl.0'));
        $this->assertEquals(trans('validation.required_without_all', ['attribute' => 'tin', 'values' => 'passport / pinfl']), $response->json('errors.0.extensions.validation.tin.0'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['passport' => 123456789])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$passport" got invalid value 123456789; String cannot represent a non string value: 123456789', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['pinfl' => 'jhgfghjk'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.integer', ['attribute' => 'pinfl']), $response->json('errors.0.extensions.validation.pinfl.0'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'pinfl', 'digits' => 14]), $response->json('errors.0.extensions.validation.pinfl.1'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['tin' => 'jhgfghjk'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.integer', ['attribute' => 'tin']), $response->json('errors.0.extensions.validation.tin.0'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'tin', 'digits' => 9]), $response->json('errors.0.extensions.validation.tin.1'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query RoyxatCredits($passport: String, $pinfl: String, $tin: String) {
                royxatCredits(passport: $passport, pinfl: $pinfl, tin: $tin) {
                    ' . $this->convertIntoQuery($this->_structure) . '
                }
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'royxatCredits' => [
                    '*' => $this->_structure,
                ],
            ],
        ]);

        $this->assertCount(1, $response->json('data.royxatCredits'));
        $this->assertIsInt($response->json('data.royxatCredits.0.id'));
        $this->assertIsString($response->json('data.royxatCredits.0.credit_id'));
        $this->assertIsInt($response->json('data.royxatCredits.0.amount'));
        $this->assertIsInt($response->json('data.royxatCredits.0.duration'));
        $this->assertIsString($response->json('data.royxatCredits.0.name'));
        $this->assertIsString($response->json('data.royxatCredits.0.passport'));
        $this->assertIsInt($response->json('data.royxatCredits.0.tin'));
        $this->assertIsString($response->json('data.royxatCredits.0.pinfl'));
        $this->assertIsString($response->json('data.royxatCredits.0.credit_status'));
        $this->assertIsInt($response->json('data.royxatCredits.0.contracted_date'));
        $this->assertIsInt($response->json('data.royxatCredits.0.overdue_days'));
        $this->assertIsInt($response->json('data.royxatCredits.0.merchant_id'));
        $this->assertIsString($response->json('data.royxatCredits.0.merchant_name'));
        $this->assertIsInt($response->json('data.royxatCredits.0.created_at'));
        $this->assertIsInt($response->json('data.royxatCredits.0.updated_at'));
    }
}
