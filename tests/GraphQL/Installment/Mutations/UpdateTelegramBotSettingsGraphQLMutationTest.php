<?php

namespace GraphQL\Installment\Mutations;

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
        $variables = ['id' => $companySettings->id, 'value' => (string) ($companySettings->value + 1)];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateTelegramBotSettingsResponse($response, $companySettings, $variables);
    }

    #[Test]
    public function successUpdateTelegramBotToken()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::TELEGRAM_BOT_TOKEN);
        $variables = ['id' => $companySettings->id, 'value' => 'new-bot-token'];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateTelegramBotSettingsResponse($response, $companySettings, $variables);
    }

    #[Test]
    public function successUpdateTelegramChatId()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::TELEGRAM_CHAT_ID);
        $variables = ['id' => $companySettings->id, 'value' => 'new-chat-id'];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateTelegramBotSettingsResponse($response, $companySettings, $variables);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['errors' => ['*' => ['message']]]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value" of required type "String!" was not provided.'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['id' => 999999999, 'value' => Str::random(256)]));
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['errors' => ['*' => ['message']]]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'value', 'max' => 255]), $response->json('errors.0.extensions.validation.value.0'));
    }

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::TELEGRAM_BOT_ENABLED);
        $variables = ['id' => $companySettings->id, 'value' => (string) ($companySettings->value + 1)];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation UpdateTelegramBotSettings(\$id: Int!, \$value: String!) {
                    updateTelegramBotSettings(id: \$id, value: \$value) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessUpdateTelegramBotSettingsResponse(TestResponse $response, CompanySettings $companySettings, array $variables)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['updateTelegramBotSettings' => $this->_structure]]);

        $companySettings->refresh();
        $this->assertEquals($variables['value'], $companySettings->value);

        $this->assertEquals($companySettings->id, $response->json('data.updateTelegramBotSettings.id'));
        $this->assertEquals($companySettings->name->value, $response->json('data.updateTelegramBotSettings.name'));
        $this->assertEquals($companySettings->name->getLabel(), $response->json('data.updateTelegramBotSettings.label'));
        $this->assertEquals($companySettings->type->value, $response->json('data.updateTelegramBotSettings.type'));
        $this->assertEquals($companySettings->value, $response->json('data.updateTelegramBotSettings.value'));
    }
}
