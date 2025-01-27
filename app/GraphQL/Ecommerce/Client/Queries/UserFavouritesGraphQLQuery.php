<?php

namespace App\GraphQL\Ecommerce\Client\Queries;

use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\Models\Company\Ecommerce\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class UserFavouritesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'userFavourites',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:ecommerce_users,user_id'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
    }

    /**
     * @throws GeneralException
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $authUser = auth()->user()->id;
        if ($authUser !== $args['user_id']) {
            throw new GeneralException(trans('validation.exists', ['attribute' => 'user id']));
        }

        return User::query()->findOrFail($authUser)->favourites;
    }
}
