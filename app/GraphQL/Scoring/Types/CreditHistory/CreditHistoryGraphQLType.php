<?php

namespace App\GraphQL\Scoring\Types\CreditHistory;

use App\GraphQL\Scoring\Types\BaseKatmHistoryGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CreditHistoryGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CreditHistoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        $fields = BaseKatmHistoryGraphQLType::fields();
        $fields['response'] = [
            'type' => GraphQL::type(KatmCreditHistoryResponseGraphQLType::GRAPHQL_TYPE_NAME),
            'description' => 'Request of Credit History scoring',
            'resolve' => function (ScoringRequest $root) {
                return $root->response;
            },
        ];

        return $fields;
    }
}
