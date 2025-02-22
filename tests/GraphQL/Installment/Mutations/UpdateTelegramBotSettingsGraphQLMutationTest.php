<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\CompanySettings;
use Database\Seeders\Company\CompanySettingsSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateTelegramBotSettingsGraphQLMutationTest extends GraphQLTestCase
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
    public function successUpdateTelegramBotEnabled()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::TELEGRAM_BOT_ENABLED);
        $value = (string) ($companySettings->value + 1);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['bot_enabled' => $value]));

        $this->assertSuccessUpdateTelegramBotSettingsResponse($response, $companySettings, $value);
    }

    #[Test]
    public function successUpdateTelegramBotToken()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::TELEGRAM_BOT_TOKEN);
        $value = 'new-bot-token';

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['bot_token' => $value]));

        $this->assertSuccessUpdateTelegramBotSettingsResponse($response, $companySettings, $value);
    }

    #[Test]
    public function successUpdateTelegramChatId()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::TELEGRAM_CHAT_ID);
        $value = 'new-chat-id';

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['chat_id' => $value]));

        $this->assertSuccessUpdateTelegramBotSettingsResponse($response, $companySettings, $value);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['bot_enabled' => 1, 'bot_token' => 1, 'chat_id' => 1])
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['errors' => ['*' => ['message']]]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$bot_enabled" got invalid value 1; String cannot represent a non string value: 1'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$bot_token" got invalid value 1; String cannot represent a non string value: 1'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$chat_id" got invalid value 1; String cannot represent a non string value: 1'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'bot_enabled' => Str::random(256),
                    'bot_token' => Str::random(256),
                    'chat_id' => Str::random(256),
                ])
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['errors' => ['*' => ['message']]]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'bot enabled', 'max' => 255]), $response->json('errors.0.extensions.validation.bot_enabled.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'bot token', 'max' => 255]), $response->json('errors.0.extensions.validation.bot_token.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'chat id', 'max' => 255]), $response->json('errors.0.extensions.validation.chat_id.0'));
    }

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::TELEGRAM_BOT_TOKEN);
        $value = 'new-bot-token';

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['bot_token' => $value]));

        $response->assertOk();
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation UpdateTelegramBotSettings(
                    \$bot_enabled: String
                    \$bot_token: String
                    \$chat_id: String
                ) {
                    updateTelegramBotSettings(
                        bot_enabled: \$bot_enabled
                        bot_token: \$bot_token
                        chat_id: \$chat_id
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessUpdateTelegramBotSettingsResponse(TestResponse $response, CompanySettings $companySettings, $value)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['updateTelegramBotSettings' => ['*' => $this->_structure]]]);

        $telegramBotSettings = CompanySettings::query()
            ->whereIn('name', CompanySettingsEnum::telegramBotSettings())
            ->orderBy('id')
            ->get();
        $this->assertEquals($telegramBotSettings->count(), count($response->json('data.updateTelegramBotSettings')));

        foreach ($telegramBotSettings as $index => $item) {
            $this->assertEquals($item->id, $response->json("data.updateTelegramBotSettings.{$index}.id"));
            $this->assertEquals($item->name->value, $response->json("data.updateTelegramBotSettings.{$index}.name"));
            $this->assertEquals($item->name->getLabel(), $response->json("data.updateTelegramBotSettings.{$index}.label"));
            $this->assertEquals($item->type->value, $response->json("data.updateTelegramBotSettings.{$index}.type"));
            $this->assertEquals($item->value, $response->json("data.updateTelegramBotSettings.{$index}.value"));
        }

        $companySettings->refresh();
        $this->assertEquals($value, $companySettings->value);
    }
}
