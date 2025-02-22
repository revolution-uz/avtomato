<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Ecommerce\UserCart;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class RemoveCartGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'removeCart',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'variation_id' => [
                'name' => 'variation_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(UserCart::class, 'variation_id')->where('user_id', auth('ecommerce-client')->id()),
                ],
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return UserCart::clearCart(auth('ecommerce-client')->id(), $args['variation_id']);
    }
}
