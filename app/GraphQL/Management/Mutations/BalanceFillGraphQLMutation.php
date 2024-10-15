<?php

namespace App\GraphQL\Management\Mutations;

use App\DTOs\Atmos\CardPay\AtmosCreatePaymentResponseDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Exceptions\GeneralException;
use App\GraphQL\Management\Types\BalanceFillDataGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
use App\Services\User\BalanceService;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class BalanceFillGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'balanceFill',
    ];

    public function __construct(
        private readonly BalanceService $balanceService
    ) {}

    public function args(): array
    {
        return [
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|digits:16',
            ],
            'expiry' => [
                'name' => 'expiry',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|digits:4',
            ],
            'amount' => [
                'name' => 'amount',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(BalanceFillDataGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BALANCE_FUND_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): AtmosCreatePaymentResponseDTO
    {
        try {
            return $this->balanceService->requestFillBalance(...$args);
        } catch (GeneralException $e) {
            throw new Error($e->getMessage());
        }
    }
}
