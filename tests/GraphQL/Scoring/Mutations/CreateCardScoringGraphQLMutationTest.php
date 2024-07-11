<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateCardScoringGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_humoInput = [
        'number' => '9860353506066262',
        'expiry' => '0430',
    ];
    private array $_uzInput = [
        'number' => '8600353506066262',
        'expiry' => '0430',
    ];

    private int $_humoType = ScoringRequestTypeEnum::HUMO->value;
    private int $_uzType = ScoringRequestTypeEnum::UZCARD->value;

    protected function setUp(): void
    {
        parent::setUp();
        Customer::query()->forceDelete();
        Card::query()->forceDelete();
        ScoringRequest::query()->forceDelete();
    }

    #[Test]
    public function successHumeCardCreate(): void
    {
        Http::fake([
            'https://api.abrand.uz/partner/katm/humo*' => Http::response(
                json_decode(file_get_contents(storage_path('../JSON-for-testing/atmos/card/bind-card-init.json')), true),
                200, // Status code
                ['Content-Type' => 'application/json'] // Headers
            ),
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_humoInput, type: $this->_humoType)
            );
        $this->assertSuccessCreateHumoCardScoringResponse($response);
    }

    #[Test]
    public function successUzCardCreate(): void
    {
        Http::fake([
            'https://api.abrand.uz/partner/katm/humo*' => Http::response(
                json_decode(file_get_contents(storage_path('../JSON-for-testing/atmos/card/bind-card-init.json')), true),
                200, // Status code
                ['Content-Type' => 'application/json'] // Headers
            ),
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_uzInput, type: ScoringRequestTypeEnum::UZCARD->value)
            );
        $this->assertSuccessCreateUzCardScoringResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: [
                    'number' => '9860353506066262',
                    'expiry' => 1231,
                ], type: $this->_humoType)
            );

        $response->assertOk();

        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$data" got invalid value 1231 at "data.expiry"; String cannot represent a non string value: 1231'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery([
                    'number' => '9860353506066262',
                    'expiry' => '0000',
                ], 1)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.in', ['attribute' => 'type']), $response->json('errors.0.extensions.validation')['type'][0]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery([
                    'number' => '315039531300',
                    'expiry' => '123',
                ], $this->_humoType)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.number', 'digits' => 16]), $response->json('errors.0.extensions.validation')['data.number'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.expiry', 'digits' => 4]), $response->json('errors.0.extensions.validation')['data.expiry'][0]);
    }

    private function buildGraphQLQuery(array $data, int $type): array
    {
        $query = [];
        $query['variables'] = ['data' => $data, 'type' => $type];
        $query['query'] = '
            mutation ($data: CardCreateInputGraphQLType!, $type: Int!) {
                createCardScoring(data: $data, type: $type)
            }
        ';

        return $query;
    }

    private function assertSuccessCreateUzCardScoringResponse(TestResponse $response): void
    {
        $this->assertResponse($response);
        $scoringRequest = ScoringRequest::query()->find($response->json('data.createCardScoring'));
        $this->assertNotNull($scoringRequest);
        $this->assertEquals($this->_uzType, $scoringRequest->type->value);
        $this->assertEquals($this->_uzInput['number'], $scoringRequest->request->number);
        $this->assertEquals($this->_uzInput['expiry'], $scoringRequest->request->expiry);
    }

    private function assertSuccessCreateHumoCardScoringResponse(TestResponse $response): void
    {
        $this->assertResponse($response);
        $scoringRequest = ScoringRequest::query()->find($response->json('data.createCardScoring'));
        $this->assertNotNull($scoringRequest);
        $this->assertEquals($this->_humoType, $scoringRequest->type->value);
        $this->assertEquals($this->_humoInput['number'], $scoringRequest->request->number);
        $this->assertEquals($this->_humoInput['expiry'], $scoringRequest->request->expiry);
    }

    private function assertResponse($response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createCardScoring',
            ],
        ]);
    }
}
