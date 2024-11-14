<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Exceptions\GeneralException;
use App\Models\Company\Ecommerce\UserFavourite;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class RemoveFavouriteGraphQLMutation extends Mutation
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
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:ecommerce_products,id',
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
        $authUser = auth('ecommerce-client')->user();
        if (is_null($authUser->customer_id)) {
            throw new GeneralException('Iltimos profilni to\'ldiring');
        }

        return UserFavourite::removeCart($authUser->id, $args['product_id']);
    }
}
