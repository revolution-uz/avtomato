<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleTransactionGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleTransaction',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TransactionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_VIEW);

        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    function ($attribute, $value, $fail) use ($branches) {
                        $exists = Transaction::query()->where('id', $value)
                            ->when($branches, fn ($q) => $q->whereHas('installment', fn ($q) => $q->whereIn('branch_id', $branches)))
                            ->exists();

                        if (! $exists) {
                            $fail(trans('validation.exists', ['attribute' => $attribute]));
                        }
                    },
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Transaction::query()->findOrFail($args['id']);
    }
}
