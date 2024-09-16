<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use App\Models\Company\Tag;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Mutation;

class DetachTagGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachTag',
    ];

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'tag_id' => [
                'name' => 'tag_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:tags,id'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_TAG_DETACH);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $customer = Customer::query()->findOrFail($args['customer_id']);
        // Check if each tag exists for the customer

        if (! $customer->tags()->where('tag_id', $args['tag_id'])->exists()) {
            throw ValidationException::withMessages(['tag_id' => __('api.not found')]);
        }

        $customer->tags()->detach($args['tag_id']);

        return true;
    }
}
