<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\AddressTypeEnum;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\Models\Company\Ecommerce\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachAddressGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachAddress',
    ];

    public function args(): array
    {
        return [
            'address' => [
                'name' => 'address',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:128',
            ],
            'region_id' => [
                'name' => 'region_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int|exists:regions,id',
            ],
            'district_id' => [
                'name' => 'district_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int|exists:districts,id',
            ],
            'mahalla_id' => [
                'name' => 'mahalla_id',
                'type' => Type::int(),
                'rules' => 'nullable|int|exists:mahallas,id',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AddressGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var User $user */
        $user = auth('ecommerce-client')->user();

        $args['type'] = AddressTypeEnum::DELIVERY_PLACE;

        return $user->addresses()->create($args);
    }
}
