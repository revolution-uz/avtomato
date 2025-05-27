<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\GraphQL\Ecommerce\Client\Types\UserCartGraphQLType;
use App\Models\Company\Ecommerce\ProductVariation;
use App\Models\Company\Ecommerce\UserCart;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AddCartGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addCart',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(UserCartGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'variation_id' => [
                'name' => 'variation_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductVariation::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'quantity' => [
                'name' => 'quantity',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    'min:1',
                ],
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return UserCart::addCart(auth('ecommerce-client')->id(), $args['variation_id'], $args['quantity']);
    }
}
