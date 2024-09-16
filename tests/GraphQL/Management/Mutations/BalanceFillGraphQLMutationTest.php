<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\FeatureEnum;
use App\Models\BaseFeature;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\Feature;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\Fakers\SmsFaker;
use Tests\GraphQL\GraphQLTestCase;

class BalanceFillGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        $feature = Feature::findByName(FeatureEnum::ATMOS_CARD_PAY);
        $feature->data = [
            'username' => 'username',
            'password' => 'password',
            'store_id' => '1',
            'expires_in' => 3600,
            'access_token' => 'access_token',
            'token_type' => 'token_type',
        ];
        $feature->saveOrFail();

        $baseFeature = BaseFeature::query()->where('name', FeatureEnum::SMS)->firstOrFail();
        $baseFeature->original_data = [
            'provider' => 'sms_sayqal',
            'username' => 'username',
            'secret' => 'secret',
        ];
        $baseFeature->saveOrFail();

        AtmosFaker::getToken();
        SmsFaker::sayqal();
    }

    #[Test]
    public function successBalanceFill()
    {
        AtmosFaker::attachCard();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['number' => 9860082443467252, 'expiry' => '2808']),
            );

        $this->assertSuccessResponse($response);
        $this->assertEquals(0, CardConfirmationSms::query()->count());
    }

    #[Test]
    public function successBalanceFillWithAttachedCard()
    {
        AtmosFaker::attachCard(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['number' => 9860082443467252, 'expiry' => '2808']),
            );

        $this->assertSuccessResponse($response);
        $this->assertEquals(1, CardConfirmationSms::query()->count());
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation BalanceFill($number: BigInteger!, $expiry: String!) {
                balanceFill(number: $number, expiry: $expiry) {
                    transaction_id
                    phone
                    result_code
                    result_description
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
                'balanceFill' => [
                    'transaction_id',
                    'phone',
                    'result_code',
                    'result_description',
                ],
            ],
        ]);
    }
}
