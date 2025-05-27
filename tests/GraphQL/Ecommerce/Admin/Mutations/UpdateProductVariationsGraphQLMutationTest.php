<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductOptionValue;
use App\Models\Company\Ecommerce\ProductVariation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateProductVariationsGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'is_active',
        'product_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'product' => [
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
        'option_values' => [
            '*' => [
                'id',
                'value_uz',
                'value_ru',
                'value_en',
                'value_oz',
                'slug',
                'option_id',
                'image_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
    ];

    #[Test]
    public function successUpdateProductVariations()
    {
        $productVariation = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$productVariation]));

        $this->assertSuccessUpdateProductVariationsResponse($response, $productVariation);
    }

    #[Test]
    public function successUpdateProductVariationsWithOnlyRequiredAttributes()
    {
        $productVariation = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$productVariation]));

        $this->assertSuccessUpdateProductVariationsResponse($response, $productVariation, true);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $productVariation = [
            'id' => null,
            'is_active' => null,
            'cost' => null,
            'actual_price' => null,
            'old_price' => null,
            'images' => null,
            'values' => null,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$productVariation]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].id"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].is_active"; Expected non-nullable type "Boolean!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].cost"; Expected non-nullable type "Float!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].actual_price"; Expected non-nullable type "Float!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].values"; Expected non-nullable type "[Int]!" not to be null.'));

        // Rule errors
        $productVariation = $this->getInputVariables(false, [
            'id' => 999999999,
            'images' => [
                [
                    'image_id' => 999999999,
                    'order' => -1,
                ],
            ],
            'values' => [
                999999999,
            ],
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$productVariation]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product variations.0.id']), $response->json('errors.0.extensions.validation')['productVariations.0.id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product variations.0.images.0.image id']), $response->json('errors.0.extensions.validation')['productVariations.0.images.0.image_id'][0]);
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'product variations.0.images.0.order', 'min' => 0]), $response->json('errors.0.extensions.validation')['productVariations.0.images.0.order'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'productVariations.0.values.0']), $response->json('errors.0.extensions.validation')['productVariations.0.values.0'][0]);
    }

    #[Test]
    public function errorUpdateProductVariationsWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$this->getInputVariables()]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    protected function buildGraphQLQuery(array $productVariations = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation UpdateProductVariations(\$productVariations: [UpdateProductVariationInputGraphQLType]!) {
                    updateProductVariations(productVariations: \$productVariations) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => ['productVariations' => $productVariations],
        ];
    }

    private function assertSuccessUpdateProductVariationsResponse(TestResponse $response, array $data, bool $onlyRequired = false): void
    {
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'updateProductVariations' => [
                    '*' => $this->_structure,
                ],
            ],
        ]);

        $productVariation = ProductVariation::query()->with('optionValues', 'images')->find($data['id']);

        $this->assertEquals($productVariation->is_active, $data['is_active']);
        $this->assertEquals($productVariation->cost, $data['cost']);
        $this->assertEquals($productVariation->actual_price, $data['actual_price']);
        ! $onlyRequired ?
            $this->assertEquals($productVariation->old_price, $data['old_price']) :
            $this->assertNull($productVariation->old_price);

        $this->assertEquals($productVariation->optionValues->count(), count($data['values']));
        foreach ($data['values'] as $value_id) {
            $this->assertNotNull(
                Arr::first($productVariation->optionValues, fn (ProductOptionValue $value) => $value->id === $value_id)
            );
        }

        $images = $data['images'] ?? [];
        $this->assertEquals($productVariation->images->count(), count($images));
        foreach ($images as $image_data) {
            $this->assertNotNull(
                Arr::first($productVariation->images, fn (EcommerceImage $image) => $image->id === $image_data['image_id'] && $image->pivot?->order === $image_data['order'])
            );
        }
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        /** @var ProductVariation $variation */
        $variation = ProductVariation::factory()->create();

        /** @var EcommerceImage[] $images */
        $images = EcommerceImage::factory()->count(2)->create();

        /** @var ProductOptionValue[]|Collection<ProductOptionValue> $values */
        $values = ProductOptionValue::factory()->count(3)->create();

        $variation->optionValues()->attach(
            $values->mapWithKeys(
                fn (ProductOptionValue $value) => [$value->id => ['option_id' => $value->option_id, 'product_id' => $variation->product_id]]
            )
        );

        return array_merge(
            [
                'id' => $variation->id,
                'is_active' => (bool) rand(0, 1),
                'cost' => random_int(1, 100000) / 100,
                'actual_price' => random_int(1, 100000) / 100,
                'old_price' => ! $isRequired ? random_int(1, 100000) / 100 : null,
                'images' => ! $isRequired ? [
                    [
                        'image_id' => $images[1]->id,
                        'order' => 1,
                    ],
                    [
                        'image_id' => $images[0]->id,
                        'order' => 2,
                    ],
                ] : null,
                'values' => $values->pluck('id'),
            ],
            $variables
        );
    }
}
