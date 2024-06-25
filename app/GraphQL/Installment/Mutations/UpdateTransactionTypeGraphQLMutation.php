<?php

namespace App\GraphQL\TransactionType\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\TransactionType\Types\TransactionTypeInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\TransactionType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateTransactionTypeGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTransactionType',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'exists:transaction_types,id',
            ],
            'transactionType' => [
                'name' => 'transactionType',
                'type' => Type::nonNull(GraphQL::type(TransactionTypeInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TARIFF_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if ($args['id'] < 100) {
            abort(403, 'You can not edit system transaction types');
        }

        /** @var TransactionType $transactionType */
        $transactionType = TransactionType::findOrFail($args['id']);
        $transactionType->fill($args['transactionType']);
        $transactionType->setNames($args['transactionType']['name']);
        $transactionType->saveOrFail();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
