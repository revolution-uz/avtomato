<?php

namespace App\GraphQL;

use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

trait GraphQLTypeHelper
{
    public static function type(): Type&NullableType
    {
        /** @var Type&NullableType $type */
        $type = GraphQL::type(self::GRAPHQL_TYPE_NAME);

        return $type;
    }
}
