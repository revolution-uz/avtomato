<?php

namespace App\GraphQL\TransactionType\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\TransactionType\Types\TransactionTypeGraphQLType;
use App\GraphQL\TransactionType\Types\TransactionTypeInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\TransactionType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateTransactionTypeGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTransactionType',
    ];

    public function args(): array
    {
        return [
            'transactionType' => [
                'name' => 'transactionType',
                'type' => Type::nonNull(GraphQL::type(TransactionTypeInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TransactionTypeGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TARIFF_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $transactionType = new TransactionType;
        $transactionType->fill($args['transactionType']);
        $transactionType->setNames($args['transactionType']['name']);
        $transactionType->is_system = false;
        $transactionType->saveOrFail();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
