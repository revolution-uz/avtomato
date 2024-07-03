<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateTransactionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTransaction',
    ];

    public function args(): array
    {
        return [
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id'),
                ],
            ],
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('customers', 'id'),
                ],
            ],
            'transaction_type_id' => [
                'name' => 'transaction_type_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('transaction_types', 'id'),
                    function ($attribute, $value, $fail) {
                        if ($value < 100) {
                            $fail(__('api.You cannot create system payment manually'));
                        }
                    },
                ],
            ],
            'amount' => [
                'name' => 'amount',
                'type' => Type::nonNull(Type::float()),
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
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = new Transaction;
        $model->fill($args);
        $model->external_id = 'local_' . Str::random();
        $model->employee_id = auth()->id();
        if (! $model->paid_at) {
            $model->paid_at = Carbon::now();
        }
        $model->saveOrFail();

        return $model;
    }
}
