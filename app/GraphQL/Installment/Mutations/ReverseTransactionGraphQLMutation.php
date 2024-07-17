<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use App\Services\AtmosService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

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
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('transactions', 'id')->where(function ($query) {
//                        $query->where('transaction_type_id', '>=', 100);
                    }),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_REVERSE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $transaction = Transaction::findOrFail($args['id']);
        if ($transaction->transaction_type_id === TransactionType::ATMOS_WITH_CARD || $transaction->transaction_type_id === TransactionType::ATMOS_WITH_PINFL) {
            return $this->atmosCardService->reversePayment($transaction);
        } else {
            return $transaction->delete();
        }
    }
}
