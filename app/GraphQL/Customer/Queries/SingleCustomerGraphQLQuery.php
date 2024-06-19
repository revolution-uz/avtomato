<?php

namespace App\GraphQL\Customer\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleCustomerGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleCustomer',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Customer::query()->findOrFail($args['id']);
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => 'int|exists:customers,id',
        ];
    }
}
