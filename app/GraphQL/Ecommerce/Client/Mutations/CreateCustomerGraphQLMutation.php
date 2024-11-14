<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\CustomerTypeEnum;
use App\Exceptions\GeneralException;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserGraphQLType;
use App\Models\Company\Ecommerce\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateCustomerGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCustomer',
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
        /** @var User $user */
        $args['customer']['type'] = CustomerTypeEnum::INSTALLMENT_AND_ECOMMERCE;
        if ($customer = $user->customer) {
            $customer->updateOrFail($args['customer']);
        } else {
            $user->customer()->create($args['customer']);
        }

        return $user;
    }
}
