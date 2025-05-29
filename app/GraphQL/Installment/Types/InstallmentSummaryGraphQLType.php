<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentSummaryGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentSummaryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'items' => [
                'type' => GraphQL::paginate(InstallmentSummaryItemsGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (array $root) => $root['items'],
            ],
            'totals' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentSummaryTotalsGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['totals'],
            ],
        ];
    }
}
