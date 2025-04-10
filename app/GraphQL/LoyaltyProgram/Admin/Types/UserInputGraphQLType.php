<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\Rules\PhoneRule;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class UserInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'UserInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'digits:12', new PhoneRule],
            ],
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'birth_date' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d',
            ],
        ];
    }
}
