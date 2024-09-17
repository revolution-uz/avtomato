<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use App\Services\AtmosService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class ReverseTransactionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'reverseTransaction',
    ];

    public function __construct(
        private readonly AtmosService $atmosCardService,
    ) {}

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_REVERSE);

        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    function ($attribute, $value, $fail) use ($branches) {
                        /** @var Builder $transactionQuery */
                        $transactionQuery = Transaction::query()->where('id', $value)
                            ->when($branches, fn ($q) => $q->whereHas('installment', fn ($q) => $q->whereIn('branch_id', $branches)));
                        /** @var Transaction $transaction */
                        $transaction = $transactionQuery->first();
                        if ($transactionQuery->doesntExist()) {
                            $fail(__('validation.exists', ['attribute' => $attribute]));
                        } elseif ($transaction->status === TransactionStatusEnum::CANCELED) {
                            $fail(__('api.already_canceled_transaction'));
                        }
                        //                        elseif ($transactionQuery->where('transaction_type_id', '<', 100)->exists()) {
                        //                            $fail(__('api.it is system payment'));
                        //                        }
                    },
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_REVERSE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $transaction = Transaction::findOrFail($args['id']);
        if ($transaction->transaction_type_id === TransactionType::ATMOS_WITH_CARD || $transaction->transaction_type_id === TransactionType::ATMOS_WITH_PINFL) {
            return $this->atmosCardService->reversePayment($transaction);
        } else {
            $installment = $transaction->installment;
            $installment->paid_amount -= $transaction->amount;
            $installment->saveOrFail();

            $transaction->status = TransactionStatusEnum::CANCELED;
            $transaction->saveOrFail();

            return true;
        }
    }
}
