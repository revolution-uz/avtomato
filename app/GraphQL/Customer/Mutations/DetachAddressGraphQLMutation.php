<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Address;
use App\Models\Company\Customer;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Mutation;

class DetachAddressGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachAddress',
    ];

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int()))),
                'rules' => ['required', 'array', 'distinct'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $customer = Customer::query()->with('addresses')->findOrFail($args['customer_id']);

        if ($customer->addresses->count() === 1) {
            throw ValidationException::withMessages(['ids.0' => __('api.Cannot detach the only address associated with the customer')]);
        }

        $customer->addresses()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids.*' => [
                'required',
                'int',
                Rule::exists(Address::class, 'id')
                    ->where('addressable_type', Customer::class)
                    ->where('addressable_id', $args['customer_id'])
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
