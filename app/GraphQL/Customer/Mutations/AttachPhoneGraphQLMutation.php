<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use App\Rules\PhoneRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachPhoneGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachPhone',
    ];

    public function args(): array
    {
        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:customers,id'],
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['distinct', 'required', 'int', new PhoneRule],
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int|in:' . implode(',', PhoneTypeEnum::values()),
            ],
            'comment' => [
                'name' => 'comment',
                'type' => Type::string(),
                'rules' => 'string|max:64',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(PhoneGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $customer = Customer::findOrFail($args['customer_id']);
        /** @var Phone $phone */
        $phone = Phone::query()->firstOrCreate(['phone' => $args['phone']]);

        $customer->phones()->attach($phone->id, ['type' => $args['type'], 'comment' => $args['comment'] ?? null]);
        $customer->refresh();

        return $customer->phones()->where('phone', $args['phone'])->first();
    }
}
