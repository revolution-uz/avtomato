<?php

namespace App\GraphQL\Management\Mutations;

use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\AttachCardDataGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Helpers\RbacHelper;
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
            'amount' => [
                'name' => 'amount',
                'type' => Type::nonNull(Type::int()),
                'rules' => 'required|int',
            ],
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|string',
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|string',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachCardDataGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BALANCE_FUND_CONFIRM);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $result = $this->balanceService->fundBalance(BalanceFundConfirmDTO::from($args));

        if (! $result) {
            throw ValidationException::withMessages(['otp' => 'Error on confirming payment']);
        }

        return $result;
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
