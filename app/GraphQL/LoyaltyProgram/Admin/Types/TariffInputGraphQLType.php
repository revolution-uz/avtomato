<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class TariffInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'TariffInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string', 'max:255', 'min:3'],
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string', 'max:255', 'min:3'],
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => ['required', 'int', 'min:0', 'max:1000000000'],
            ],
            'free_days' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'min:0'],
            ],
            'subscription_days' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'min:0'],
            ],
            'is_free_for_previous_subscribers' => [
                'type' => Type::nonNull(Type::boolean()),
                'rules' => ['required', 'boolean'],
            ],
            'limit_amount' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'min:0', 'max:1000000000'],
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'rules' => ['required', 'boolean'],
            ],
        ];
    }
}
