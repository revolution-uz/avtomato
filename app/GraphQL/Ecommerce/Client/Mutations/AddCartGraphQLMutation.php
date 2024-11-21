<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Client\Types\UserCartGraphQLType;
use App\Models\Company\Ecommerce\UserCart;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

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
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:ecommerce_products,id',
                ],
            ],
            'quantity' => [
                'name' => 'quantity',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                ],
            ],
        ];
    }

    /**
     * @throws GeneralException
     * @throws Throwable
     */
    public function resolve($root, $args)
    {
        if (! auth('ecommerce-client')->check()) {
            throw new AuthorizationError('Unauthenticated');
        }

        return UserCart::addCart(auth('ecommerce-client')->user()->id, $args['product_id'], $args['quantity'] ?? 1);
    }
}
