<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionDataDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionInfoResponseDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionRowDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionSizePensionDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Spatie\LaravelData\DataCollection;
use Tests\GraphQL\GraphQLTestCase;

class SinglePensionInfoGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private ScoringRequest $_scoring_request;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        $this->_scoring_request = ScoringRequest::factory()->create(
            [
                'type' => ScoringRequestTypeEnum::KATM_PENSION_INFO,
                'status' => ScoringRequestStatusEnum::SUCCESS,
                'request' => new KatmCreateRequestDTO('30906997180039', 'AB', '4143286', Carbon::createFromDate('15-06-2016'), 'Akmal', 'Baxodirov', 'Azamat', '998882019909', '998882019909', Carbon::createFromDate('09-06-1999'), 13, 114),
                'response' => new KatmPensionInfoResponseDTO(
                    '1',
                    new KatmPensionSizePensionDTO(
                        new KatmPensionDataDTO('Muhiba', 'BOBOATO1 12 12 12', '1926720.03', 'Qamarovna', 'AA1737639', '01', 'Ergasheva', '4 yil 0 oy 0 kun', '1', '8', '42405621120018', '24.05.2016'),
                        new DataCollection(
                            KatmPensionRowDTO::class,
                            [
                                new KatmPensionRowDTO('2022/10', 'Oktyabr', 1926720.03),
                                new KatmPensionRowDTO('2022/10', 'Oktyabr', 1926720.03),
                            ]
                        ),
                    ),
                    'Неявляется получателем пенсии (пособия)'

                ),
            ]
        );
    }

    #[Test]
    public function successSinglePensionInfo()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->_scoring_request->id)
            );
        $this->assertSuccessSinglePensionInfoResponse($response);
    }

    #[Test]
    public function errorSinglePensionInfo()
    {
        // not found
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(99999999)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_scoring_request->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->_scoring_request->id)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $query['query'] = '
    query ($id: Int!) {
        singlePensionInfo(id: $id) {
            id
            ext_id
            status
            type
            client_code
            customer_id
            card_id
            created_at
            updated_at
            request {
               pinfl
            }
        }
    }';

        return $query;
    }

    private function assertSuccessSinglePensionInfoResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singlePensionInfo' => [
                    'id',
                    'ext_id',
                    'status',
                    'type',
                    'client_code',
                    'customer_id',
                    'card_id',
                    'created_at',
                    'updated_at',
                    'request' => [
                        'pinfl',
                    ],
                ],
            ],
        ]);
        $scoring = ScoringRequest::query()->find($response->json('data.singlePensionInfo.id'));
        $this->assertArrayHasKey('id', $response->json('data.singlePensionInfo'));
        $this->assertEquals($scoring->id, $response->json('data.singlePensionInfo.id'));
    }
}
