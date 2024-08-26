<?php

namespace Tests\GraphQL\Common\Queries;

use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\BalanceFund;
use App\Models\Company\CompanySettings;
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
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery()
            );

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
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.card_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.pinfl_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.guarantor_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.guarantor_pinfl_after_due_days'));
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
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.card_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.pinfl_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.guarantor_after_due_days'));
        $this->assertEquals($settings->first(fn (CompanySettings $setting) => $setting->name === CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->value ?? 0, $response->json('data.settings.guarantor_pinfl_after_due_days'));
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
}
