<?php

namespace App\GraphQL\QrPay\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\QrPay\Types\TransactionGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\CompanyQrPayService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateTransactionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTransaction',
    ];

    public function __construct(private readonly CompanyQrPayService $qrPayService) {}

    public function args(): array
    {
        return [
            'amount' => [
                'name' => 'amount',
                'type' => Type::nonNull(Type::float()),
                'rules' => 'required|numeric|min:1',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TransactionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::QR_PAY_TRANSACTION_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->qrPayService->createPayment($args['amount']);
    }
}
