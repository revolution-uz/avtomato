<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use Database\Seeders\Company\CompanySettingsSeeder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class TelegramBotSettingsGraphQLQueryTest extends GraphQLTestCase
{
    private array $_structure = [
        'id',
        'name',
        'label',
        'type',
        'value',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => CompanySettingsSeeder::class]);
    }

    #[Test]
    public function successTelegramBotSettings()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());

        $this->assertSuccessTelegramBotSettingsResponse($response);
    }

    #[Test]
    public function successSettingsWithLanguages()
    {
        // Default - Russian
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());
        $this->assertEquals('Отправить сообщение через Телеграм бот', $response->json('data.telegramBotSettings.0.label'));

        // English
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(), ['Accept-Language' => 'en']);
        $this->assertEquals('Send a message via Telegram bot', $response->json('data.telegramBotSettings.0.label'));

        // Russian
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(), ['Accept-Language' => 'ru']);
        $this->assertEquals('Отправить сообщение через Телеграм бот', $response->json('data.telegramBotSettings.0.label'));

        // Uzbek - Latin
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(), ['Accept-Language' => 'uz']);
        $this->assertEquals('Telegram bot orqali xabar yuborish', $response->json('data.telegramBotSettings.0.label'));

        // Uzbek - Cyrillic
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(), ['Accept-Language' => 'oz']);
        $this->assertEquals('Телеграм бот орқали хабар юбориш', $response->json('data.telegramBotSettings.0.label'));
    }

    public function buildGraphQLQuery(): array
    {
        return [
            'query' => <<<GRAPHQL
                query TelegramBotSettings {
                    telegramBotSettings {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
        ];
    }

    private function assertSuccessTelegramBotSettingsResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['telegramBotSettings' => ['*' => $this->_structure]]]);

        $telegramBotSettings = CompanySettings::query()
            ->whereIn('name', CompanySettingsEnum::telegramBotSettings())
            ->orderBy('id')
            ->get();
        $this->assertEquals($telegramBotSettings->count(), count($response->json('data.telegramBotSettings')));

        foreach ($telegramBotSettings as $index => $item) {
            $this->assertEquals($item->id, $response->json("data.telegramBotSettings.{$index}.id"));
            $this->assertEquals($item->name->value, $response->json("data.telegramBotSettings.{$index}.name"));
            $this->assertEquals($item->name->getLabel(), $response->json("data.telegramBotSettings.{$index}.label"));
            $this->assertEquals($item->type->value, $response->json("data.telegramBotSettings.{$index}.type"));
            $this->assertEquals($item->value, $response->json("data.telegramBotSettings.{$index}.value"));
        }
    }
}
