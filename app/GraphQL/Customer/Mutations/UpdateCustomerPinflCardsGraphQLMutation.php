<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\CustomerTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use App\Services\UniredService;
use App\Services\User\BalanceService;
use Closure;
use Exception;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Mutation;

class UpdateCustomerPinflCardsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCustomerPinflCards',
    ];

    public function __construct(private UniredService $uniredService, private BalanceService $balanceService) {}

    public function args(): array
    {
        return [
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(Customer::class, 'id')
                        ->where('type', CustomerTypeEnum::INSTALLMENT)
                        ->whereNotNull('pinfl')
                        ->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::UPDATE_CUSTOMER_PINFL_CARDS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): true
    {
        $customer = Customer::query()->findOrFail($args['customer_id']);

        if (
            $customer->installments()->whereNotNull('unired_id')->doesntExist() &&
            $customer->guaranteedInstallments()->whereNotNull('installment_guarantors.unired_id')->doesntExist()
        ) {
            throw ValidationException::withMessages(['customer_id' => __('Customer does not have any installments or guaranteed installments')]);
        }

        try {
            $this->uniredService->manualUpdatePinflCardsList($customer);
            $this->uniredService->getAndSyncPinflCards($customer);
            $this->balanceService->chargeAmount(FeatureEnum::UNIRED_CARD_MANAGEMENT, $customer);

            return true;

        } catch (Exception $e) {
            throw new Error($e->getMessage());
        }
    }
}
