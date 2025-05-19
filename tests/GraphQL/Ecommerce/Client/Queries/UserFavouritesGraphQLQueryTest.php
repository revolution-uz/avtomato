<?php

namespace Tests\GraphQL\Ecommerce\Client\Queries;

use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserFavourite;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UserFavouritesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private int $_totalUserFavouritesCount = 5;

    private array $_structure = [
        'id',
        'name',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'cost',
        'actual_price',
        'old_price',
        'is_active',
        'category_id',
        'brand_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'category' => [
            'id',
            'name',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'parent_id',
            'image_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'brand' => [
            'id',
            'name',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'slug',
            'is_active',
            'image_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'images' => [
            '*' => [
                'id',
                'url',
                'original_name',
                'mime',
                'order',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
        'options' => [
            '*' => [
                'id',
                'name',
                'name_uz',
                'name_ru',
                'name_en',
                'name_oz',
                'slug',
                'description',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
        'variations' => [
            '*' => [
                'id',
                'is_active',
                'cost',
                'actual_price',
                'old_price',
                'product_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();

        UserFavourite::query()->forceDelete();
        UserFavourite::factory()->count($this->_totalUserFavouritesCount)->create(['user_id' => $this->_user->id]);
    }

    #[Test]
    public function successUserFavourites(): void
    {
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());

        $this->assertSuccessUserFavouritesResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query UserFavourites {
                    userFavourites {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessUserFavouritesResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['userFavourites' => ['*' => $this->_structure]]]);

        $this->assertCount($this->_totalUserFavouritesCount, $response->json('data.userFavourites'));

        /** @var Product $userFavourite */
        $userFavourite = $this->_user->favourites()->orderByPivot('id', 'desc')->first();

        $this->assertEquals($userFavourite->id, $response->json('data.userFavourites.0.id'));
        $this->assertEquals($userFavourite->name, $response->json('data.userFavourites.0.name'));
        $this->assertEquals($userFavourite->name_uz, $response->json('data.userFavourites.0.name_uz'));
        $this->assertEquals($userFavourite->name_ru, $response->json('data.userFavourites.0.name_ru'));
        $this->assertEquals($userFavourite->name_en, $response->json('data.userFavourites.0.name_en'));
        $this->assertEquals($userFavourite->name_oz, $response->json('data.userFavourites.0.name_oz'));
        $this->assertEquals($userFavourite->slug, $response->json('data.userFavourites.0.slug'));
        $this->assertEquals($userFavourite->cost, $response->json('data.userFavourites.0.cost'));
        $this->assertEquals($userFavourite->actual_price, $response->json('data.userFavourites.0.actual_price'));
        $this->assertEquals($userFavourite->old_price, $response->json('data.userFavourites.0.old_price'));
        $this->assertEquals($userFavourite->is_active, $response->json('data.userFavourites.0.is_active'));
        $this->assertEquals($userFavourite->category_id, $response->json('data.userFavourites.0.category_id'));
        $this->assertEquals($userFavourite->brand_id, $response->json('data.userFavourites.0.brand_id'));
        $this->assertEquals($userFavourite->created_at->timestamp, $response->json('data.userFavourites.0.created_at'));
        $this->assertEquals($userFavourite->updated_at->timestamp, $response->json('data.userFavourites.0.updated_at'));
        $this->assertEquals($userFavourite->deleted_at?->timestamp, $response->json('data.userFavourites.0.deleted_at'));
    }
}
