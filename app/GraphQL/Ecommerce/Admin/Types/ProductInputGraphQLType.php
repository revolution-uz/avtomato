<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class ProductInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'ProductInputGraphQLType';

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
                'rules' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if (Product::query()->where('slug', Str::slug($value))->exists()) {
                            $fail(trans('validation.unique', ['attribute' => 'name']));
                        }
                    },
                ],
            ],
            'name_oz' => [
                'name' => 'name_oz',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'cost' => [
                'name' => 'cost',
                'type' => Type::nonNull(Type::float()),
                'rules' => 'required|numeric|min:0',
            ],
            'actual_price' => [
                'name' => 'actual_price',
                'type' => Type::nonNull(Type::float()),
                'rules' => 'required|numeric|min:0',
            ],
            'old_price' => [
                'name' => 'old_price',
                'type' => Type::float(),
                'rules' => 'nullable|numeric|min:0',
            ],
            'is_active' => [
                'name' => 'is_active',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => 'required|boolean',
            ],
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
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
            'images' => [
                'name' => 'images',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(ProductImageInputGraphQLType::type()))),
            ],
        ];
    }
}
