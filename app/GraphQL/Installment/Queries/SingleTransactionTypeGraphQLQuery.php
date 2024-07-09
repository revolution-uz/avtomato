<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionTypeGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\TransactionType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleTransactionTypeGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleTransactionType',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TransactionTypeGraphQLType::GRAPHQL_TYPE_NAME));
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
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_TYPE_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return TransactionType::query()->findOrFail($args['id']);
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                Rule::exists('transaction_types', 'id')
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
