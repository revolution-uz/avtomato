<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class SubscriptionInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'SubscriptionInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'rules' => ['required', 'bool'],
            ],
            'tariff_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:loyalty_program_tariffs,id,deleted_at,NULL'],
            ],
        ];
    }
}
