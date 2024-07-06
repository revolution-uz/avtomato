<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateCardScoringGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input = [
        'number' => '9860350106916132',
        'expiry' => '0427',
    ];

    private int $_type = ScoringRequestTypeEnum::HUMO->value;

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
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_input, type: $this->_type)
            );
        $this->assertSuccessCreateCardScoringResponse($response);
    }
    public function successUzCardCreate(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: [
                    'number' => '8600353506066262',
                    'expiry' => '0427',
                ], type: ScoringRequestTypeEnum::UZCARD->value)
            );
        $this->assertSuccessCreateCardScoringResponse($response);
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
                ], type: $this->_type)
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
                    'expiry' => '1231',
                ], $this->_type)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.number', 'digits' => 16]), $response->json('errors.0.extensions.validation')['data.number'][0]);
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

    private function assertSuccessCreateCardScoringResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createCardScoring',
            ],
        ]);
        $scoringRequest = ScoringRequest::query()->find($response->json('data.createCardScoring'));
        $this->assertNotNull($scoringRequest);
        $this->assertEquals($this->_type, $scoringRequest->type->value);
        $this->assertEquals($this->_input['number'], $scoringRequest->request->number);
        $this->assertEquals($this->_input['expiry'], $scoringRequest->request->expiry);
    }
}
