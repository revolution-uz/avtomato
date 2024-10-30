<?php

namespace App\GraphQL\Scoring\Types\UzumNasiya;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UzumNasiyaLimitGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UzumNasiyaLimitGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {

        return BaseScoringRequestGraphQLType::fields() + [
            'response' => [
                'type' => GraphQL::type(UzumNasiyaLimitResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ScoringRequest $root) => $root->response,
            ],
            'request' => [
                'type' => GraphQL::type(UzumNasiyaLimitCreateInputGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ScoringRequest $root) => $root->request,
            ],
        ];
    }
}
