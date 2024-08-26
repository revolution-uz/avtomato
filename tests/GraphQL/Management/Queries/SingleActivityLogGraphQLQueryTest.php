<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\ActivityLog;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleActivityLogGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'admin_id',
        'created_at',
        'description',
        'admin' => [
            'id',
            'username',
            'first_name',
            'last_name',
            'phone',
            'status',
            'language',
            'role_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'role' => [
                'id',
                'name',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        ActivityLog::query()->forceDelete();
        ActivityLog::factory()->count(30)->create([
            'causer_id' => $this->createAdminUser()->id,
            'causer_type' => get_class($this->createAdminUser()),
        ]);
        ActivityLog::query()->whereNull('causer_id')->forceDelete();
    }

    #[Test]
    public function successWithId()
    {
        $activityLog = ActivityLog::query()->with('causer')->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $activityLog->id])
            );

        $this->assertSuccessSingleActivityLogResponse($response, $activityLog);
    }

    #[Test]
    public function errorInvalidId()
    {
        $id = ActivityLog::query()->orderByDesc('id')->first()->id;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $id + 1])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
    }

    #[Test]
    public function errorValidationId()
    {
        $id = 'hjebfwef';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$id\" got invalid value \"{$id}\"; Int cannot represent non-integer value: \"{$id}\"", $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query SingleActivityLog($id: Int!) {
                singleActivityLog(id: $id) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessSingleActivityLogResponse(TestResponse $response, ?ActivityLog $activityLog): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleActivityLog' => $this->_structure,
            ],
        ]);

        $this->assertNotNull($activityLog);
        $this->assertEquals($activityLog->id, $response->json('data.singleActivityLog.id'));
        $this->assertEquals($activityLog->causer_id, $response->json('data.singleActivityLog.admin_id'));
        $this->assertEquals($activityLog->created_at->timestamp, $response->json('data.singleActivityLog.created_at'));
        $this->assertEquals($this->logMessageBuilder($activityLog->subject_type, $activityLog->subject_id, $activityLog->event->value), $response->json('data.singleActivityLog.description'));

        $this->assertEquals($activityLog->causer->id, $response->json('data.singleActivityLog.admin.id'));
        $this->assertEquals($activityLog->causer->username, $response->json('data.singleActivityLog.admin.username'));
        $this->assertEquals($activityLog->causer->first_name, $response->json('data.singleActivityLog.admin.first_name'));
        $this->assertEquals($activityLog->causer->last_name, $response->json('data.singleActivityLog.admin.last_name'));
        $this->assertEquals($activityLog->causer->phone, $response->json('data.singleActivityLog.admin.phone'));
        $this->assertEquals($activityLog->causer->status->value, $response->json('data.singleActivityLog.admin.status'));
        $this->assertEquals($activityLog->causer->language, $response->json('data.singleActivityLog.admin.language'));
        $this->assertEquals($activityLog->causer->role_id, $response->json('data.singleActivityLog.admin.role_id'));
        $this->assertEquals($activityLog->causer->created_at->timestamp, $response->json('data.singleActivityLog.admin.created_at'));
        $this->assertEquals($activityLog->causer->updated_at?->timestamp, $response->json('data.singleActivityLog.admin.updated_at'));
        $this->assertEquals($activityLog->causer->deleted_at?->timestamp, $response->json('data.singleActivityLog.admin.deleted_at'));

        $this->assertEquals($activityLog->causer->role->id, $response->json('data.singleActivityLog.admin.role.id'));
        $this->assertEquals($activityLog->causer->role->name, $response->json('data.singleActivityLog.admin.role.name'));
        $this->assertEquals($activityLog->causer->role->created_at->timestamp, $response->json('data.singleActivityLog.admin.role.created_at'));
        $this->assertEquals($activityLog->causer->role->updated_at?->timestamp, $response->json('data.singleActivityLog.admin.role.updated_at'));
        $this->assertEquals($activityLog->causer->role->deleted_at?->timestamp, $response->json('data.singleActivityLog.admin.role.deleted_at'));
    }

    private function convertIntoQuery(array $structure): string
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if ($key == '*') {
                    $structure[$key] = $this->convertIntoQuery($value);
                } else {
                    $structure[$key] = "{$key} {{$this->convertIntoQuery($value)}}";
                }
            }
        }

        return implode(' ', $structure);
    }

    private function logMessageBuilder($modelName, $modelId, $event): string
    {
        $modelName = trans('models.' . $modelName);

        return trans('logging.' . $event, ['entity' => $modelName, 'id' => $modelId]);
    }
}
