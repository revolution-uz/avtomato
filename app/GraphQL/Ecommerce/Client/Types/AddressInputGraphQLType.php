<?php

namespace App\GraphQL\Customer\Types;

use App\Enums\Company\AddressTypeEnum;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\InputType;

class AddressInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'AddressInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::in(AddressTypeEnum::values()),
                ],
            ],
            'address' => [
                'name' => 'address',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ],
            'region_id' => [
                'name' => 'region_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::exists('regions', 'id'),
                ],
            ],
            'district_id' => [
                'name' => 'district_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::exists('districts', 'id'),
                ],
            ],
            'mahalla_id' => [
                'name' => 'mahalla_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'integer',
                    Rule::exists('mahallas', 'id'),
                ],
            ],
        ];
    }
}
