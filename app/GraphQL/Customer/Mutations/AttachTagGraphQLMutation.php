<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\AttachTagGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachTagGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachTag',
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
        return Type::nonNull(GraphQL::type(AttachTagGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_TAG_ATTACH);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $customer = Customer::findOrFail($args['customer_id']);
        $customerTags = $customer->tags()->where('tag_id', $args['tag_id'])->first();
        if ($customerTags) {
            throw ValidationException::withMessages(['tag_id' => 'Tag already attached']);
        }
        $customer->tags()->attach($args['tag_id']);
        $customer->refresh();

        return $customer;
    }
}
