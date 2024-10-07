<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Mutation;

class DeleteTransactionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTransaction',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        Transaction::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_DELETE);

        return [
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($branches) {
                    /** @var Builder $transactionQuery */
                    $transactionQuery = Transaction::query()->where('id', $value)
                        ->where('is_initial_fee', false)
                        ->when($branches, fn ($q) => $q->whereHas('installment', fn ($q) => $q->whereIn('branch_id', $branches)));

                    if ($transactionQuery->doesntExist()) {
                        $fail(__('validation.exists', ['attribute' => $attribute]));
                    } elseif ($transactionQuery->where('transaction_type_id', '<', 100)->exists()) {
                        $fail(__('api.it is system payment'));
                    }
                },
            ],
        ];
    }
}
