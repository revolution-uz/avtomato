<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\AddressTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
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
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int|in:' . implode(',', AddressTypeEnum::values()),
            ],
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

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $customer = Customer::findOrFail($args['customer_id']);

        return $customer->addresses()->create($args);
    }
}
