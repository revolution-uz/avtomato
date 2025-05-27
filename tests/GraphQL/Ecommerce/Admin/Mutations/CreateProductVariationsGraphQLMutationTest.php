<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductOptionValue;
use App\Models\Company\Ecommerce\ProductVariation;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateProductVariationsGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successCreateProductVariations()
    {
        $productVariation = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$productVariation]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['createProductVariations']]);
        $this->assertTrue($response->json('data.createProductVariations'));
        $this->assertSuccessCreateProductVariationsResponse($productVariation);
    }

    #[Test]
    public function successCreateProductVariationsWithOnlyRequiredAttributes()
    {
        $productVariation = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$productVariation]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['createProductVariations']]);
        $this->assertTrue($response->json('data.createProductVariations'));
        $this->assertSuccessCreateProductVariationsResponse($productVariation, true);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $productVariation = [
            'product_id' => null,
            'is_active' => null,
            'cost' => null,
            'actual_price' => null,
            'values' => null,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([$productVariation]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].product_id"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].is_active"; Expected non-nullable type "Boolean!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].cost"; Expected non-nullable type "Float!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].actual_price"; Expected non-nullable type "Float!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$productVariations" got invalid value null at "productVariations[0].values"; Expected non-nullable type "[Int!]!" not to be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'product variations']), $response->json('errors.0.extensions.validation.productVariations.0'));

        $productVariation = $this->getInputVariables(false, [
            'product_id' => 999999999,
            'images' => [
                [
                    'image_id' => 999999999,
                    'order' => 1,
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
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product variations.0.product id']), $response->json('errors.0.extensions.validation')['productVariations.0.product_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product variations.0.images.0.image id']), $response->json('errors.0.extensions.validation')['productVariations.0.images.0.image_id'][0]);
        // $this->assertEquals(trans('validation.exists', ['attribute' => 'product variations.0.values.0.image id']), $response->json('errors.0.extensions.validation')['productVariations.0.values.0'][0]);
    }

    #[Test]
    public function errorCreateProductVariationsWithInactiveCompany(): void
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
            'query' => <<<'GRAPHQL'
                mutation CreateProductVariations($productVariations: [ProductVariationInputGraphQLType!]!) {
                    createProductVariations(productVariations: $productVariations)
                }
            GRAPHQL,
            'variables' => ['productVariations' => $productVariations],
        ];
    }

    private function assertSuccessCreateProductVariationsResponse(array $data, bool $onlyRequired = false): void
    {
        $productVariation = ProductVariation::query()->with('optionValues', 'images')->firstOrFail();

        $this->assertEquals($productVariation->product_id, $data['product_id']);
        $this->assertEquals($productVariation->is_active, $data['is_active']);
        $this->assertEquals($productVariation->cost, $data['cost']);
        $this->assertEquals($productVariation->actual_price, $data['actual_price']);
        if (! $onlyRequired) {
            $this->assertEquals($productVariation->old_price, $data['old_price']);
        } else {
            $this->assertNull($productVariation->old_price);
        }

        $this->assertEquals($productVariation->optionValues->count(), count($data['values']));
        foreach ($data['values'] as $value_id) {
            $this->assertNotNull(
                Arr::first($productVariation->optionValues, fn (ProductOptionValue $value) => $value->id === $value_id)
            );
        }

        if (! $onlyRequired) {
            $this->assertEquals($productVariation->images->count(), count($data['images']));
            foreach ($data['images'] as $image_data) {
                $this->assertNotNull(
                    Arr::first($productVariation->images, fn (EcommerceImage $image) => $image->id === $image_data['image_id'] && $image->pivot?->order === $image_data['order'])
                );
            }
        } else {
            $this->assertEquals($productVariation->images->count(), 0);
        }
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        /** @var EcommerceImage[] $images */
        $images = EcommerceImage::factory()->count(2)->create();

        return array_merge(
            [
                'product_id' => Product::factory()->create()->id,
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
                'values' => [
                    ProductOptionValue::factory()->create()->id,
                    ProductOptionValue::factory()->create()->id,
                ],
            ],
            $variables
        );
    }
}
