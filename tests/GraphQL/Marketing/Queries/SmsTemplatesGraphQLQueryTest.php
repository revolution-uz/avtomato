<?php

namespace Tests\GraphQL\Marketing\Queries;

use App\Enums\Company\SmsTemplateEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use App\Models\Company\SmsTemplate;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SmsTemplatesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'description',
        'example',
        'created_at',
        'updated_at',
    ];

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.marketing'), $this->buildGraphQLQuery());

        $this->assertSuccessSmsTemplatesResponse($response);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.marketing'), $this->buildGraphQLQuery(['sort' => 'id']));

        $this->assertSuccessSmsTemplatesResponse($response);
        $smsTemplateIds = SmsTemplate::query()->orderBy('id')->pluck('id')->toArray();
        $this->assertEquals($smsTemplateIds, $response->json('data.smsTemplates.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.marketing'), $this->buildGraphQLQuery(['sort' => '-id']));

        $this->assertSuccessSmsTemplatesResponse($response);
        $smsTemplateIds = SmsTemplate::query()->orderByDesc('id')->pluck('id')->toArray();
        $this->assertEquals($smsTemplateIds, $response->json('data.smsTemplates.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $smsTemplate = SmsTemplate::factory()->create();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.marketing'), $this->buildGraphQLQuery(['search' => $smsTemplate->template]));

        $this->assertSuccessSmsTemplatesResponse($response, 1);
        $this->assertEquals($smsTemplate->id, $response->json('data.smsTemplates.0.id'));
    }

    #[Test]
    public function errorSort()
    {
        // GraphQL type error
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.marketing'), $this->buildGraphQLQuery(['sort' => 111]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" got invalid value 111; String cannot represent a non string value: 111', $response->json('errors.0.message'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.marketing'), $this->buildGraphQLQuery(['sort' => 'non-exist-column']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    #[Test]
    public function successSmsTemplatesWhenEcommerceIsInactive()
    {
        $feature = Feature::findByName(FeatureEnum::ECOMMERCE);
        $feature->update(['is_active' => false]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.marketing'), $this->buildGraphQLQuery());

        $total = SmsTemplate::query()->whereNotIn('id', [SmsTemplateEnum::ECOMMERCE_OTP->value])->count();
        $response->assertJsonMissing(['id' => $feature->id]);
        $this->assertSuccessSmsTemplatesResponse($response, $total);
    }

    public function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query SmsTemplates(\$sort: String, \$search: String) {
                    smsTemplates(sort: \$sort, search: \$search) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSmsTemplatesResponse(TestResponse $response, ?int $total = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['smsTemplates' => ['*' => $this->_structure]]]);

        $this->assertEquals($total ?? SmsTemplate::query()->count(), count($response->json('data.smsTemplates')));
        foreach ($response->json('data.smsTemplates') as $r) {
            $smsTemplate = SmsTemplate::query()->findOrFail($r['id']);
            $this->assertEquals($smsTemplate->description, $r['description']);
            $this->assertEquals($smsTemplate->getExampleMessage(), $r['example']);
            $this->assertEquals($smsTemplate->created_at->timestamp, $r['created_at']);
            $this->assertEquals($smsTemplate->updated_at->timestamp, $r['updated_at']);
        }
    }
}
