<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Scalars\BigInteger;
use App\Rules\TariffPartValidations;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class InstallmentInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'tariff_id' => [
                'name' => 'tariff_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('tariffs', 'id')->whereNull('deleted_at'),
                ],
            ],
            'period' => [
                'name' => 'period',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    new TariffPartValidations,
                ],
            ],
            'amount' => [
                'name' => 'amount',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'numeric',
                    new TariffPartValidations,
                ],
            ],
            'initial_fee' => [
                'name' => 'initial_fee',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'numeric',
                    new TariffPartValidations,
                ],
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                ],
            ],
            'starts_in' => [
                'name' => 'starts_in',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'date',
                ],
            ],
        ];
    }
}
