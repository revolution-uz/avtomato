<?php

namespace App\GraphQL\Scoring\Types\Katm\CreditHistory;

use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryScoreDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmCreditHistoryScoreGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmCreditHistoryScoreGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Credit History Score GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'score_point' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'score point',
                'resolve' => function (KatmCreditHistoryScoreDTO $root) {
                    return $root->score_point;
                },
            ],
            'score_class' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'score class',
                'resolve' => function (KatmCreditHistoryScoreDTO $root) {
                    return $root->score_class;
                },
            ],
            'score_level' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'score level',
                'resolve' => function (KatmCreditHistoryScoreDTO $root) {
                    return $root->score_level;
                },
            ],
            'score_date' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'score date',
                'resolve' => function (KatmCreditHistoryScoreDTO $root) {
                    return $root->score_date;
                },
            ],
            'score_version' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'score version',
                'resolve' => function (KatmCreditHistoryScoreDTO $root) {
                    return $root->score_version;
                },
            ],
        ];
    }
}
