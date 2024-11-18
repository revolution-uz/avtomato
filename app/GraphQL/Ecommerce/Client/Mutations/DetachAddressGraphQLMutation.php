<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Validator;
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
                'rules' => ['required', 'array', 'distinct', function (string $attribute, mixed $value, Closure $fail) {
                    $validator = Validator::make([$attribute => $value], [
                        $attribute => 'array',
                        $attribute . '.*' => 'integer|exists:addresses,id',
                    ]);
                    if ($validator->fails()) {
                        return $fail($validator->errors());
                    }
                }, ],
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
        $customer->addresses()->whereIn('id', $args['ids'])->delete();

        return true;
    }
}
