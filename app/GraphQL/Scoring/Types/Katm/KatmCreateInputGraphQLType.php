<?php

namespace App\GraphQL\Scoring\Types\Katm;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class KatmCreateInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'KatmCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|integer|digits:14',
            ],
            'passport_series' => [
                'name' => 'passport_series',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:2|min:2',
            ],
            'passport_number' => [
                'name' => 'passport_number',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:7|min:7|regex:/^[0-9]+$/',
            ],
            'passport_given_date' => [
                'name' => 'passport_given_date',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date',
            ],
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:32',
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:32',
            ],
            'middle_name' => [
                'name' => 'middle_name',
                'type' => Type::string(),
                'rules' => 'required|string|max:32',
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|int|digits:12',
            ],
            'extra_phone' => [
                'name' => 'extra_phone',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|int|digits:12',
            ],
            'birth_date' => [
                'name' => 'birth_date',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date',
            ],
            'region_id' => [
                'name' => 'region_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int|exists:regions,id',
            ],
            'district_id' => [
                'name' => 'district_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int|exists:districts,id',
            ],
        ];
    }
}
