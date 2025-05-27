<?php

namespace App\GraphQL\Customer\Types;

use App\Enums\Company\PhoneTypeEnum;
use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class PhoneInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'PhoneInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'string',
                    'digits_between:9,13',
                    'regex:/^[0-9]+$/',
                ],
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::in(PhoneTypeEnum::customerValues()),
                ],
            ],
            'comment' => [
                'name' => 'comment',
                'type' => Type::string(),
                'rules' => [
                    'nullable',
                    'string',
                    'max:128',
                ],
            ],
        ];
    }
}
