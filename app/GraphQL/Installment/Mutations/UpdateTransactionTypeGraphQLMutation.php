<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\TransactionType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
                'rules' => Rule::exists('transaction_types', 'id')->whereNull('deleted_at'),
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:32',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_TYPE_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if ($args['id'] < 100) {
            throw ValidationException::withMessages(['ids' => 'You can not edit system transaction types']);
        }

        /** @var TransactionType $transactionType */
        $transactionType = TransactionType::findOrFail($args['id']);
        $transactionType->setNames($args['transactionType']['name']);
        $transactionType->saveOrFail();

        return true;
    }
}
