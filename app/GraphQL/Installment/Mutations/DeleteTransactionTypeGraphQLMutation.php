<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteTransactionTypeGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTransactionType',
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
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_TYPE_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        TransactionType::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value < 100) {
                        $fail(__('api.You cannot delete', [
                            'item' => TransactionType::query()->where('id', $value)->first()->name,
                        ]));
                    }
                    if (! TransactionType::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if (Transaction::query()->where('transaction_type_id', $value)->exists()) {
                        $fail(__('api.cannot_delete', [
                            'item' => __('api.Transaction Type'),
                            'element' => __('api.Transaction'),
                        ]));
                    }
                },
            ],
        ];
    }
}
