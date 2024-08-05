<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use Database\Seeders\Company\CompanySettingsSeeder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SettingsGraphQLQueryTest extends GraphQLTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => CompanySettingsSeeder::class]);
    }

    #[Test]
    public function successSettings()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(),
            );

        $this->assertSuccessSettingsResponse($response);
    }

    #[Test]
    public function successSettingsWithLanguages()
    {
        // Default - Russian
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(),
            );

        $this->assertEquals('Списание денег с карты клиента при просрочке платежа', $response->json('data.settings.0.label'));

        // English
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(),
                ['Accept-Language' => 'en']
            );

        $this->assertEquals('Writing off money from a client\'s card in case of late payment', $response->json('data.settings.0.label'));

        // Russian
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(),
                ['Accept-Language' => 'ru']
            );

        $this->assertEquals('Списание денег с карты клиента при просрочке платежа', $response->json('data.settings.0.label'));

        // Uzbek - Latin
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(),
                ['Accept-Language' => 'uz']
            );

        $this->assertEquals('To\'lov vaqtidan o\'tib ketganda mijozning kartasidan pul yechib olish', $response->json('data.settings.0.label'));

        // Uzbek - Cyrillic
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(),
                ['Accept-Language' => 'oz']

            );

        $this->assertEquals('Тўлов кечиктирилган тақдирда мижознинг картасидан пулни ҳисобдан чиқариш', $response->json('data.settings.0.label'));
    }

    private function buildGraphQLQuery(): array
    {
        $query = [];
        $query['variables'] = [];
        $query['query'] = '
            query Settings {
                settings {
                    id
                    label
                    type
                    value
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSettingsResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'settings' => [
                    '*' => [
                        'id',
                        'label',
                        'type',
                        'value',
                    ],
                ],
            ],
        ]);

        $companySettings = CompanySettings::query()->whereIn('name', CompanySettingsEnum::installmentSettings())->get();
        $this->assertEquals($companySettings->count(), count($response->json('data.settings')));

        $i = 0;
        foreach ($companySettings as $item) {
            $this->assertEquals($item->id, $response->json("data.settings.{$i}.id"));
            $this->assertEquals($item->name->getLabel(), $response->json("data.settings.{$i}.label"));
            $this->assertEquals($item->type->value, $response->json("data.settings.{$i}.type"));
            $this->assertEquals($item->value, $response->json("data.settings.{$i}.value"));
            $i++;
        }
    }
}
