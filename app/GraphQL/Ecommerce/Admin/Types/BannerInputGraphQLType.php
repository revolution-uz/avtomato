<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class BannerInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'BannerInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'name_uz' => [
                'name' => 'name_uz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'name_ru' => [
                'name' => 'name_ru',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'name_en' => [
                'name' => 'name_en',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'name_oz' => [
                'name' => 'name_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'is_active' => [
                'name' => 'is_active',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => 'required|boolean',
            ],
            'image_uz_id' => [
                'name' => 'image_uz_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(EcommerceImage::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'image_ru_id' => [
                'name' => 'image_ru_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(EcommerceImage::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'image_en_id' => [
                'name' => 'image_en_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(EcommerceImage::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'image_oz_id' => [
                'name' => 'image_oz_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(EcommerceImage::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    Rule::exists(ProductCategory::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'brand_id' => [
                'name' => 'brand_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    Rule::exists(ProductBrand::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    Rule::exists(Product::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }
}
