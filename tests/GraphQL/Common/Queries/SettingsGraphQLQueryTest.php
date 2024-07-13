<?php

namespace Tests\GraphQL\Common\Queries;

use App\Enums\Main\SettingsEnum;
use App\Models\Company\BalanceFund;
use App\Models\Settings;
use App\Services\User\BalanceService;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SettingsGraphQLQueryTest extends GraphQLTestCase
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
                'settings' => [
                    'app_maintenance_mode',
                    'web_maintenance_mode',
                    'app_min_version',
                    'balance',
                ],
            ],
        ]);

        $settings = Settings::query()->get();
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.app_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_WEB_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.web_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MIN_VERSION)->value ?? true, $response->json('data.settings.app_min_version'));
        $this->assertEquals(0, $response->json('data.settings.balance'));
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
                'settings' => [
                    'app_maintenance_mode',
                    'web_maintenance_mode',
                    'app_min_version',
                    'balance',
                ],
            ],
        ]);

        $settings = Settings::query()->get();
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.app_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_WEB_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.web_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MIN_VERSION)->value ?? true, $response->json('data.settings.app_min_version'));
        $this->assertNotEquals(0, (new BalanceService)->getBalance());
        $this->assertNotEquals(0, $response->json('data.settings.balance'));
        $this->assertEquals((new BalanceService)->getBalance(), $response->json('data.settings.balance'));
    }

    private function buildGraphQLQuery(): array
    {
        $query = [];
        $query['variables'] = [];
        $query['query'] = '
            query Settings {
                settings {
                    app_maintenance_mode
                    web_maintenance_mode
                    app_min_version
                    balance
                }
            }
        ';

        return $query;
    }
}
