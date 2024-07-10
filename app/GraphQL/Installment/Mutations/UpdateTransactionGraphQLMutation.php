<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\GraphQL\Scalars\BigFloat;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateTransactionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTransaction',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('transactions', 'id')->where(function ($query) {
                        $query->where('transaction_type_id', '>=', 100);
                    }),
                ],
            ],
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::exists('installments', 'id'),
                ],
            ],
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::exists('customers', 'id'),
                ],
            ],
            'transaction_type_id' => [
                'name' => 'transaction_type_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::exists('transaction_types', 'id'),
                ],

            ],
            'amount' => [
                'name' => 'amount',
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
            ],
            'paid_at' => [
                'name' => 'paid_at',
                'type' => Type::string(),
                'rules' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TransactionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = Transaction::findOrFail($args['id']);
        $model->updateOrFail($args);

        return $model;
    }
}
