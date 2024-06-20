<?php

namespace App\GraphQL\Common\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;

class IDsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'IDsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'IDs GraphQL Type',
    ];
}
