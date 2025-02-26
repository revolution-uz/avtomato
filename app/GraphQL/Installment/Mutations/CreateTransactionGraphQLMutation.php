<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\GraphQL\Scalars\BigFloat;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateTransactionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTransaction',
    ];

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_CREATE);

        return [
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id')->when($branches, fn ($q) => $q->whereIn('branch_id', $branches)),
                ],
            ],
            'branch_id' => [
                'name' => 'branch_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::exists('branches', 'id'),
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
                        if (TransactionType::isAutoPay($value)) {
                            $fail(__('api.You cannot create system payment manually'));
                        }
                    },
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
                'rules' => 'nullable|date_format:Y-m-d',
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
        /** @var Installment $installment */
        $installment = Installment::query()->findOrFail($args['installment_id']);

        $model = new Transaction;
        $model->fill($args);
        $model->external_id = 'local_' . Str::random();
        $model->admin_id = auth('company-admin')->id();

        if (! $model->installment->is_active && ! $model->installment->isPaidInitialFee()) {
            if ($model->amount != $model->installment->initial_fee) {
                throw ValidationException::withMessages(['amount' => __('api.Please create initial fee amount first')]);
            }
            $model->is_initial_fee = true;
        }

        if (! $model->is_initial_fee && $installment->wholeDebtAmount < $args['amount']) {
            throw ValidationException::withMessages(['amount' => __('api.max_amount', ['amount' => $installment->wholeDebtAmount])]);
        }

        if (isset($args['paid_at'])) {
            $model->paid_at = Carbon::createFromFormat('Y-m-d', $args['paid_at'])->setTime(now()->hour, now()->minute);
        } else {
            $model->paid_at = Carbon::now();
        }

        $model->branch_id = $args['branch_id'] ?? $installment->branch_id;

        $model->saveOrFail();

        return $model->refresh();
    }
}
