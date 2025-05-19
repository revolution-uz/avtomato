<?php

namespace App\GraphQL\Scoring\Types\Katm\InpsHistory;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InpsHistoryGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InpsHistoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Inps History GraphQL Type',
    ];

    public function fields(): array
    {
        return BaseScoringRequestGraphQLType::fields() + [
            'request' => [
                'type' => GraphQL::type(KatmCreateRequestGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Request of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->request;
                },
            ],
            'response' => [
                'type' => GraphQL::type(KatmInpsHistoryResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Request of Inps History scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->response;
                },
            ],
        ];
    }
}
