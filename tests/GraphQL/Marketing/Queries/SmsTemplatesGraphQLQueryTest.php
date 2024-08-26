<?php

namespace Tests\GraphQL\Marketing\Queries;

use App\Models\Company\SmsTemplate;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SmsTemplatesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.marketing'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessSmsTemplatesResponse($response);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.marketing'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessSmsTemplatesResponse($response);
        $smsTemplateIds = SmsTemplate::query()->orderBy('id')->pluck('id')->toArray();
        $this->assertEquals($smsTemplateIds, $response->json('data.smsTemplates.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.marketing'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessSmsTemplatesResponse($response);
        $smsTemplateIds = SmsTemplate::query()->orderByDesc('id')->pluck('id')->toArray();
        $this->assertEquals($smsTemplateIds, $response->json('data.smsTemplates.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $smsTemplate = SmsTemplate::query()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.marketing'),
                $this->buildGraphQLQuery(['search' => $smsTemplate->template ?? $smsTemplate->original_template])
            );

        $this->assertSuccessSmsTemplatesResponse($response, 1);
        $this->assertEquals($smsTemplate->id, $response->json('data.smsTemplates.0.id'));
    }

    #[Test]
    public function errorSort()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.marketing'),
                $this->buildGraphQLQuery(['sort' => 111])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" got invalid value 111; String cannot represent a non string value: 111', $response->json('errors.0.message'));

        // not exist column validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.marketing'),
                $this->buildGraphQLQuery(['sort' => 'non-exist-column'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query SmsTemplates($sort: String, $search: String) {
                smsTemplates(sort: $sort, search: $search) {
                    id
                    description
                    example
                    created_at
                    updated_at
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSmsTemplatesResponse(TestResponse $response, ?int $total = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'smsTemplates' => [
                    '*' => [
                        'id',
                        'description',
                        'example',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);

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
