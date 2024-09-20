<?php

namespace App\GraphQL\Management\Mutations;

use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Exceptions\AtmosException;
use App\GraphQL\Management\Types\BalanceFundGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
use App\Models\Company\BalanceFund;
use App\Services\User\BalanceService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class BalanceFillConfirmGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'balanceFillConfirm',
    ];

    public function __construct(
        private readonly BalanceService $balanceService
    ) {}

    public function args(): array
    {
        return [
            'transaction_id' => [
                'name' => 'transaction_id',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
            ],
            'otp' => [
                'name' => 'otp',
                'type' => Type::nonNull(Type::string()),
            ],
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|integer|digits:16',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(BalanceFundGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BALANCE_FUND_CONFIRM);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): BalanceFund
    {
        try {
            return $this->balanceService->fundBalance(BalanceFundConfirmDTO::from($args));
        } catch (AtmosException $e) {
            throw ValidationException::withMessages(['otp' => $e->getMessage()]);
        }
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args) + [
            'otp' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($args) {

                    if (isset($args['transaction_id'])) {
                        return;
                    }

                    //todo:  This is a fake OTP, it should be validated against the real OTP
                    if ($value !== '123456') {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                },
            ],
        ];
    }
}
