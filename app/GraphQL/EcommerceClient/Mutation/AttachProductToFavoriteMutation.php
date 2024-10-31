<?php

namespace App\GraphQL\EcommerceClient\Mutation;

use App\Exceptions\GeneralException;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Mutation;

class AttachProductToFavoriteMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachProductToFavorite',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'user_id' => [
                'name' => 'user_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:e_commerce_products,id'],
            ],
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:e_commerce_products,id'],
            ],
        ];
    }

    /**
     * @throws GeneralException
     * @throws AuthorizationError
     */
    public function resolve($root, $args)
    {
        if (! auth('ecommerce-client')->check()) {
            throw new AuthorizationError('Unauthenticated');
        }
        $user = auth('ecommerce-client')->user();
        if ($user->id !== $args['user_id']) {
            throw new GeneralException(trans('validation.exists', ['attribute' => 'user_id']));
        }
        $user->favorite()->attach($args['product_id']);

        return true;
    }
}
