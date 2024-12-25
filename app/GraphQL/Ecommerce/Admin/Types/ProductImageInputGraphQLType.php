<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\EcommerceImage;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class ProductImageInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'ProductImageInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'image_id' => [
                'name' => 'image_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(EcommerceImage::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'order' => [
                'name' => 'order',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|integer|min:0',
            ],
        ];
    }
}
