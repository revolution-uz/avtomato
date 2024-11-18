<?php

namespace App\GraphQL\Ecommerce\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProductOptionFilterInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductOptionFilterInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'option_id' => [
                'name' => 'option_id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:ecommerce_product_options,id'],
            ],
            'value_id' => [
                'name' => 'group_id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:ecommerce_product_option_values,id'],
            ],
        ];
    }
}
