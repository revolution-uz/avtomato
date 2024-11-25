<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryLoanDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditHistoryLoanGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryLoanGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => RoyxatCreditHistoryLoanDTO::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditHistoryLoanDTO $root) => $root->id,
            ],
            'amount' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditHistoryLoanDTO $root) => $root->amount,
            ],
            'contract_date' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryLoanDTO $root) => $root->contract_date,
            ],
            'max_overdue_days' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditHistoryLoanDTO $root) => $root->max_overdue_days,
            ],
            'total_overdue_days' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditHistoryLoanDTO $root) => $root->total_overdue_days,
            ],
            'total_overdue_amount' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditHistoryLoanDTO $root) => $root->total_overdue_amount,
            ],
            'status' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryLoanDTO $root) => $root->status,
            ],
        ];
    }
}
