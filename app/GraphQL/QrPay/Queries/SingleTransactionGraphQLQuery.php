<?php

namespace App\GraphQL\QrPay\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\QrPay\Types\SbpTransactionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\SbpTransaction;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleTransactionGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleTransaction',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(SbpTransactionGraphQLType::GRAPHQL_TYPE_NAME));
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
                    Rule::exists(SbpTransaction::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::QR_PAY_TRANSACTION_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return SbpTransaction::query()->findOrFail($args['id']);
    }
}
