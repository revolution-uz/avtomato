<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProductJoinOptionInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductJoinOptionInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'option_id' => [
                'name' => 'option_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:product_options,id'],
            ],
            'value_id' => [
                'name' => 'value_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:product_option_values,id'],
            ],
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:products,id'],
            ],
        ];
    }
}
