<?php

namespace Tests\GraphQL\Common\Queries;

use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\BalanceFund;
use App\Models\Company\CompanySettings;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CompanySettingsGraphQLQueryTest extends GraphQLTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        BalanceFund::query()->forceDelete();
        BalanceFund::factory()->create();
    }

    #[Test]
    public function settingsWithoutAuthentication()
    {
        $response = $this
            ->postJson(route('graphql.common'), $this->buildGraphQLQuery());

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function settingsWithAuthentication()
    {
        $admin = $this->createAdminUser();
        $token = $admin->createTokenWithData('test');
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery(),
                ['Authorization' => 'Bearer ' . $token]
            );

        $this->assertSuccessResponse($response);
    }

    private function buildGraphQLQuery(): array
    {
        $query = [];
        $query['variables'] = [];
        $query['query'] = '
            query companySettings {
                companySettings {
                    card_after_due_days
                    pinfl_after_due_days
                    guarantor_after_due_days
                    guarantor_pinfl_after_due_days
                }
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'companySettings' => [
                    'card_after_due_days',
                    'pinfl_after_due_days',
                    'guarantor_after_due_days',
                    'guarantor_pinfl_after_due_days',
                ],
            ],
        ]);

        $settings = CompanySettings::query()->get();
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value, $response->json('data.companySettings.card_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::PINFL_AFTER_DUE_DAYS)->value, $response->json('data.companySettings.pinfl_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS)->value, $response->json('data.companySettings.guarantor_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS)->value, $response->json('data.companySettings.guarantor_pinfl_after_due_days'));
    }
}
