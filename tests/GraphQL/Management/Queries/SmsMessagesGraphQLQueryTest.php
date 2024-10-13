<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\SmsMessage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SmsMessagesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalMessagesCount = 31;

    private array $_structure = [
        'id',
        'phone',
        'message',
        'status',
        'ext_id',
        'installment_id',
        'customer_id',
        'customer' => [
            'id',
            'pinfl',
            'tin',
            'passport_series',
            'passport_number',
            'passport_given_date',
            'passport_expiration_date',
            'passport_given_place',
            'passport_citizenship',
            'first_name',
            'last_name',
            'middle_name',
            'birth_date',
            'birth_place',
            'gender',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'installment_id',
        'installment' => [
            'id',
            'customer_id',
            'status',
            'branch_id',
            'admin_id',
            'tariff_id',
            'type',
            'sms_notify',
            'auto_pay',
            'auto_pay_by_pinfl',
            'is_blacklist',
            'period',
            'description',
            'cancellation_details',
            'starts_in',
            'next_pay_date',
            'created_at',
            'updated_at',
            'canceled_at',
            'deleted_at',
            'debt',
            'monthly_payment',
            'initial_fee',
        ],
        'customer_type',
        'created_at',
        'updated_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        SmsMessage::query()->forceDelete();
        SmsMessage::factory()->count($this->_totalMessagesCount)->create();
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessSmsMessagesResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $page = 2;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );

        $this->assertSuccessSmsMessagesResponse($response, page: $page);
    }

    #[Test]
    public function successPerPage()
    {
        $perPage = 25;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $perPage])
            );

        $this->assertSuccessSmsMessagesResponse($response, perPage: $perPage);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessSmsMessagesResponse($response);
        $messageIds = SmsMessage::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($messageIds, $response->json('data.smsMessages.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessSmsMessagesResponse($response);
        $messageIds = SmsMessage::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($messageIds, $response->json('data.smsMessages.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $message = SmsMessage::query()->inRandomOrder()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => (string) $message->phone])
            );

        $this->assertSuccessSmsMessagesResponse($response, total: 1);
        $this->assertEquals($message->id, $response->json('data.smsMessages.data.0.id'));
        $this->assertEquals($message->phone, $response->json('data.smsMessages.data.0.phone'));
        $this->assertEquals($message->message, $response->json('data.smsMessages.data.0.message'));
        $this->assertEquals($message->status->value, $response->json('data.smsMessages.data.0.status'));
        $this->assertEquals($message->ext_id, $response->json('data.smsMessages.data.0.ext_id'));
        $this->assertEquals($message->created_at->timestamp, $response->json('data.smsMessages.data.0.created_at'));
        $this->assertEquals($message->updated_at->timestamp, $response->json('data.smsMessages.data.0.updated_at'));
    }

    #[Test]
    public function errorPage()
    {
        $page = 'kjefnerw';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$page\" got invalid value \"{$page}\"; Int cannot represent non-integer value: \"{$page}\"", $response->json('errors.0.message'));

        $page = 0;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        $per_page = 'kjefnerw';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$per_page\" got invalid value \"{$per_page}\"; Int cannot represent non-integer value: \"{$per_page}\"", $response->json('errors.0.message'));

        $per_page = 0;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        $per_page = 101;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        $sort = 111;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => $sort])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$sort\" got invalid value {$sort}; String cannot represent a non string value: {$sort}", $response->json('errors.0.message'));

        $sort = 'non-exist-column';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => $sort])
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
            query SmsMessages($sort: String, $search: String, $page: Int, $per_page: Int) {
                smsMessages(sort: $sort, search: $search, page: $page, per_page: $per_page) {
                    total
                    per_page
                    current_page
                    from
                    to
                    last_page
                    has_more_pages
                    data {' . $this->convertIntoQuery($this->_structure) . '}
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSmsMessagesResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'smsMessages' => [
                    'total',
                    'per_page',
                    'current_page',
                    'from',
                    'to',
                    'last_page',
                    'has_more_pages',
                    'data' => [
                        '*' => $this->_structure,
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalMessagesCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.smsMessages.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.smsMessages.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.smsMessages.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.smsMessages.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.smsMessages.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.smsMessages.to'));
        $this->assertEquals($totalCount, $response->json('data.smsMessages.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.smsMessages.data'));
    }
}
