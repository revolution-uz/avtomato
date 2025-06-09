<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23ClientPhonesDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23ClientPhonesGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23ClientPhonesGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phones' => [
                'type' => Type::listOf(GraphQL::type(KatmStartInfoReport23ClientPhoneGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (KatmStartInfoReport23ClientPhonesDTO $root) => $root->phones,
            ],
        ];
    }
}
