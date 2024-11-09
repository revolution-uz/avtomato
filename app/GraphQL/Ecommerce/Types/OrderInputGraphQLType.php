<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class OrderInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'user_id' => [
                'name' => 'user_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:ecommerce_users,id',
                ],
            ],
            'address_id' => [
                'name' => 'address_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:addresses,id',
                ],
            ],
            'status_id' => [
                'name' => 'status_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:ecommerce_order_statuses,id',
                ],
            ],
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'exists:installments,id',
                ],
            ],

        ];
    }
}
