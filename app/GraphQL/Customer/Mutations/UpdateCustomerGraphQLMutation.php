<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\CustomerService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateCustomerGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCustomer',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'exists:customers,id',
            ],
            'customer' => [
                'name' => 'customer',
                'type' => Type::nonNull(GraphQL::type(CustomerInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return (new CustomerService)->saveCustomer($args['customer'], $args['id']);
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'customer.pinfl' => [
                'required',
                'string',
                'digits:14',
                Rule::unique('customers', 'pinfl')->ignore($args['id'], 'id'),
            ],
            'customer.tin' => [
                'nullable',
                'string',
                'digits:9',
            ],
            'customer.passport_number' => [
                'required',
                'string',
                'digits:7',
                Rule::unique('customers', 'passport_number')->where('passport_series', $args['customer']['passport_series'])->ignore($args['id'], 'id'),
            ],
        ]);
    }
}
