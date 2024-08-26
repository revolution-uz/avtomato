<?php

namespace Tests\GraphQL\Installment\Mutations;


use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use Database\Seeders\Company\CompanySettingsSeeder;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ChangeSettingsGraphQLMutationTest extends GraphQLTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => CompanySettingsSeeder::class]);
    }

    #[Test]
    public function successUpdateCardAfterDueDays()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::CARD_AFTER_DUE_DAYS);
        $input = [
            'settings_id' => $companySettings->id,
            'value' => (string) ($companySettings->value + 1),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input),
            );

        $this->assertSuccessChangeSettingsResponse($response, $input);
    }

    #[Test]
    public function successUpdatePinflAfterDueDays()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::PINFL_AFTER_DUE_DAYS);
        $input = [
            'settings_id' => $companySettings->id,
            'value' => (string) ($companySettings->value + 1),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input),
            );

        $this->assertSuccessChangeSettingsResponse($response, $input);
    }

    #[Test]
    public function successUpdateGuarantorAfterDueDays()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS);
        $input = [
            'settings_id' => $companySettings->id,
            'value' => (string) ($companySettings->value + 1),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input),
            );

        $this->assertSuccessChangeSettingsResponse($response, $input);
    }

    #[Test]
    public function successUpdateGuarantorPinflAfterDueDays()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS);
        $input = [
            'settings_id' => $companySettings->id,
            'value' => (string) ($companySettings->value + 1),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input),
            );

        $this->assertSuccessChangeSettingsResponse($response, $input);
    }

    #[Test]
    public function successCannotUpdateSmsInstallmentAutoCharge()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::SMS_INSTALLMENT_AUTO_CHARGE);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'settings_id' => $companySettings->id,
                    'value' => (string) (! $companySettings->value),
                ]),
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Cannot edit SMS installment auto charge setting.', $response->json('errors.0.extensions.validation.settings_id.0'));
    }

    #[Test]
    public function successUpdateSmsInstallmentNotification()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::SMS_INSTALLMENT_NOTIFICATION);
        $input = [
            'settings_id' => $companySettings->id,
            'value' => (string) (! $companySettings->value),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input),
            );

        $this->assertSuccessChangeSettingsResponse($response, $input);
    }

    #[Test]
    public function successUpdateSmsInstallmentPayment()
    {
        $companySettings = CompanySettings::query()->firstWhere('name', CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT);
        $input = [
            'settings_id' => $companySettings->id,
            'value' => (string) (! $companySettings->value),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input),
            );

        $this->assertSuccessChangeSettingsResponse($response, $input);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['settings_id' => null, 'value' => null]),
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'errors' => [
                '*' => [
                    'message',
                ],
            ],
        ]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$settings_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value" of non-null type "String!" must not be null.'));

        // Rule errors
        $response = $this
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['settings_id' => 999999999, 'value' => 'qwertyuiop']),
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'errors' => [
                '*' => [
                    'message',
                ],
            ],
        ]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'settings id']), $response->json('errors.0.extensions.validation.settings_id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation ChangeSettings($settings_id: Int!, $value: String!) {
                changeSettings(settings_id: $settings_id, value: $value) {
                    id
                    name
                    label
                    type
                    value
                }
            }
        ';

        return $query;
    }

    private function assertSuccessChangeSettingsResponse(TestResponse $response, array $input)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'changeSettings' => [
                    '*' => [
                        'id',
                        'name',
                        'label',
                        'type',
                        'value',
                    ],
                ],
            ],
        ]);

        $companySettings = CompanySettings::query()->findOrFail($input['settings_id']);
        $this->assertEquals($input['value'], $companySettings->value);
        $item = Arr::first($response->json('data.changeSettings'), fn (array $item) => $item['id'] === $input['settings_id']);
        $this->assertNotNull($item);
        $this->assertEquals($companySettings->value, $item['value']);
    }
}
