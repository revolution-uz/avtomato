<?php

namespace Tests\GraphQL\Common\Queries;

use App\Enums\Main\SettingsEnum;
use App\Enums\ModuleEnum;
use App\Models\Company\BalanceFund;
use App\Models\Company\Module;
use App\Models\Settings;
use App\Services\User\BalanceService;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SettingsGraphQLQueryTest extends GraphQLTestCase
{
    private array $_structure = [
        'app_maintenance_mode',
        'web_maintenance_mode',
        'app_min_version',
        'balance',
        'multibranch_module',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        BalanceFund::query()->forceDelete();
        BalanceFund::factory()->create();
        Module::query()->where('name', ModuleEnum::MULTIBRANCH_1)->update(['is_active' => true]);
    }

    #[Test]
    public function settingsWithoutAuthentication()
    {
        $response = $this->postJson(route('graphql.common'), $this->buildGraphQLQuery());

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['settings' => $this->_structure]]);

        $settings = Settings::query()->get();
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.app_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_WEB_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.web_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MIN_VERSION)->value ?? true, $response->json('data.settings.app_min_version'));
        $this->assertEquals(0, $response->json('data.settings.balance'));
        $this->assertNull($response->json('data.settings.multibranch_module'));
    }

    #[Test]
    public function settingsWithAuthentication()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.common'), $this->buildGraphQLQuery());

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['settings' => $this->_structure]]);

        $settings = Settings::query()->get();
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.app_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_WEB_MAINTENANCE_MODE)->value ?? true, $response->json('data.settings.web_maintenance_mode'));
        $this->assertEquals($settings->first(fn (Settings $setting) => $setting->name === SettingsEnum::PARTNER_APP_MIN_VERSION)->value ?? true, $response->json('data.settings.app_min_version'));
        $this->assertEquals(BalanceService::getBalance(), $response->json('data.settings.balance'));
        $this->assertEquals(ModuleEnum::MULTIBRANCH_1->value, $response->json('data.settings.multibranch_module'));
    }

    public function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Settings {
                    settings {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
