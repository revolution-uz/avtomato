<?php

namespace App\GraphQL\Scoring\Types\InpsHistory;

use App\GraphQL\Scoring\Types\BaseKatmHistoryGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InpsHistoryGraphQLType extends BaseKatmHistoryGraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InpsHistoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Inps History GraphQL Type',
    ];

    public function fields(): array
    {
        $fields = parent::fields();
        $fields['response'] = [
            'type' => GraphQL::type(KatmInpsHistoryResponseGraphQLType::GRAPHQL_TYPE_NAME),
            'description' => 'Request of Inps History scoring',
            'resolve' => function (ScoringRequest $root) {
                return $root->response;
            },
        ];

        return $fields;
    }
}
