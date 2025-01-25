<?php

namespace App\GraphQL\Customer\Types;

use App\Enums\GenderEnum;
use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class CustomerInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'CustomerInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|digits:14',
            ],
            'tin' => [
                'name' => 'tin',
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'rules' => [
                    'nullable',
                    'string',
                    'digits:9',
                ],
            ],
            'passport_series' => [
                'name' => 'passport_series',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'size:2',
                    'regex:/^[A-Z]+$/',
                ],
            ],
            'passport_number' => [
                'name' => 'passport_number',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'digits:7',
                ],
            ],
            'passport_given_date' => [
                'name' => 'passport_given_date',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d',
            ],
            'passport_expiration_date' => [
                'name' => 'passport_expiration_date',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d',
            ],
            'passport_given_place' => [
                'name' => 'passport_given_place',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'nullable',
                    'string',
                    'max:128',
                ],
            ],
            'passport_citizenship' => [
                'name' => 'passport_citizenship',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'nullable',
                    'string',
                    'max:32',
                ],
            ],
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:32',
                ],
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:32',
                ],
            ],
            'middle_name' => [
                'name' => 'middle_name',
                'type' => Type::string(),
                'rules' => [
                    'nullable',
                    'string',
                    'max:32',
                ],
            ],
            'birth_date' => [
                'name' => 'birth_date',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d',
            ],
            'birth_place' => [
                'name' => 'birth_place',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'nullable',
                    'string',
                    'max:32',
                ],
            ],
            'gender' => [
                'name' => 'gender',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::in(GenderEnum::values()),
                ],
            ],
        ];
    }
}
