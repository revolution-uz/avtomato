<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class StartInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'StartInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return BaseScoringRequestGraphQLType::fields() + [
            'request' => [
                'type' => GraphQL::type(KatmCreateRequestGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ScoringRequest $root) => $root->request,
            ],
            'response' => [
                'type' => GraphQL::type(KatmStartInfoResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ScoringRequest $root) => $root->response,
            ],
        ];
    }
}
