<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
            'image_uz' => [
                'name' => 'image_uz',
                'type' => GraphQL::type('Upload'),
                'rules' => 'required|mimes:jpg,jpeg,png|max:1024',
            ],
            'image_ru' => [
                'name' => 'image_ru',
                'type' => GraphQL::type('Upload'),
                'rules' => 'nullable|mimes:jpg,jpeg,png|max:1024',
            ],
            'image_en' => [
                'name' => 'image_en',
                'type' => GraphQL::type('Upload'),
                'rules' => 'nullable|mimes:jpg,jpeg,png|max:1024',
            ],
            'image_oz' => [
                'name' => 'image_oz',
                'type' => GraphQL::type('Upload'),
                'rules' => 'nullable|mimes:jpg,jpeg,png|max:1024',
            ],
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::int(),
                'rules' => 'nullable|exists:ecommerce_product_categories,id',
            ],
            'brand_id' => [
                'name' => 'brand_id',
                'type' => Type::int(),
                'rules' => 'nullable|exists:ecommerce_product_brands,id',
            ],
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::int(),
                'rules' => 'nullable|exists:ecommerce_products,id',
            ],
        ];
    }
}
