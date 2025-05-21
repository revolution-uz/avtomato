<?php

namespace App\GraphQL\Scoring\Types\Katm\PensionHistory;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PensionInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PensionInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Pension Info GraphQL Type',
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
                'type' => GraphQL::type(KatmPensionInfoResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Response of pension info scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->response;
                },
            ],
        ];
    }
}
