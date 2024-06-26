<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DetachPhoneGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachPhone',
    ];

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'phones' => [
                'name' => 'phones',
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
        $customer = Customer::findOrFail($args['customer_id']);
        foreach ($args['phones'] as $phone) {
            /** @var Phone $phone */
            $phone = Phone::query()->where('phone', $phone)->firstOrFail();
            $customer->phones()->detach($phone->id);
        }

        return true;
    }
}
