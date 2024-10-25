<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class UserFavoriteInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'UserFavoriteInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'user_id' => [
                'name' => 'user_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:users,id'],
            ],
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:products,id'],
            ],
        ];
    }
}
