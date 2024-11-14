<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Exceptions\GeneralException;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserGraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class FillProfileGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'fillProfile',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(UserGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'customer' => [
                'name' => 'customer',
                'type' => Type::nonNull(GraphQL::type(CustomerInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    /**
     * @throws AuthorizationError
     * @throws GeneralException
     */
    public function resolve($root, $args)
    {
        if (! auth('ecommerce-client')->check()) {
            throw new AuthorizationError('Unauthenticated');
        }
        $user = auth('ecommerce-client')->user();

        $user->customer()->create($args['customer']);

        return $user;
    }
}
