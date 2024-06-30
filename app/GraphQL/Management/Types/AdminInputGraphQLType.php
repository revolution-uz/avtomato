<?php

namespace App\GraphQL\Management\Types;

use App\Enums\Company\UserStatusEnum;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class AdminInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'AdminInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'username' => [
                'name' => 'username',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ],
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ],
            'role_id' => [
                'name' => 'role_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::exists('roles', 'id'),
                ],
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    'size:7',
                    Rule::in(UserStatusEnum::values()),
                ],
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:128',
                    'confirmed',
                ],
            ],
        ];
    }
}
