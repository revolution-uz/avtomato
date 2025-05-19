<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\UserFavourite;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class RemoveFavouriteGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'removeFavourite',
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
                    Rule::exists(Product::class, 'id')->whereNull('deleted_at'),
                    function ($attribute, $value, $fail) {
                        if (
                            UserFavourite::query()
                                ->where('user_id', auth('ecommerce-client')->id())
                                ->where('product_id', $value)
                                ->doesntExist()
                        ) {
                            $fail('The ' . $attribute . ' is not in your favourite list.');
                        }
                    },
                ],
            ],
        ];
    }

    public function resolve($root, $args): bool
    {
        return UserFavourite::query()
            ->where('user_id', auth('ecommerce-client')->id())
            ->where('product_id', $args['product_id'])
            ->delete();
    }
}
