<?php

namespace Tests\GraphQL\Customer\Queries;

use Database\Seeders\Company\LocationSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\PlumFaker;
use Tests\GraphQL\GraphQLTestCase;

class InfoByPassportGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input;
    private array $_structure = [
        'pinfl',
        'tin',
        'passport_series',
        'passport_number',
        'passport_given_date',
        'passport_expiration_date',
        'passport_given_place',
        'passport_given_org',
        'passport_citizenship_id',
        'passport_citizenship',
        'passport_living_place',
        'first_name',
        'last_name',
        'middle_name',
        'full_name',
        'birth_date',
        'birth_place',
        'gender',
        'address' => [
            'address',
            'region_id',
            'district_id',
            'mahalla_id',
            'region' => [
                'id',
                'name',
            ],
            'district' => [
                'id',
                'region_id',
                'name',
            ],
            'mahalla' => [
                'id',
                'district_id',
                'name',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('tenants:seed', [
            '--class' => LocationSeeder::class,
            '--tenants' => [tenant()->id],
        ]);

        $this->_input = [
            'passport_series' => 'AD',
            'passport_number' => '3119359',
            'birth_date' => '15-03-1995',
        ];
    }

    #[Test]
    public function successInfoByPassport()
    {
        PlumFaker::getInfoByPassport();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.customer'), $this->buildGraphQLQuery($this->_input));

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'infoByPassport' => $this->_structure,
            ],
        ]);
    }

    #[Test]
    public function errorValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'passport_series' => null,
                    'passport_number' => null,
                    'birth_date' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_series" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_number" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$birth_date" of non-null type "String!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'passport_series' => '12',
                    'passport_number' => 'kmnjhgv',
                    'birth_date' => 'huygtfyghui',
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'passport series']), $response->json('errors.0.extensions.validation.passport_series.0'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'passport number']), $response->json('errors.0.extensions.validation.passport_number.0'));
        $this->assertEquals(trans('validation.date', ['attribute' => 'birth date']), $response->json('errors.0.extensions.validation.birth_date.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query InfoByPassport(
                $passport_series: String!
                $passport_number: String!
                $birth_date: String!
            ) {
                infoByPassport(
                    passport_series: $passport_series
                    passport_number: $passport_number
                    birth_date: $birth_date
                ) {
                    ' . $this->convertIntoQuery($this->_structure) . '
                }
            }
        ';

        return $query;
    }
}
