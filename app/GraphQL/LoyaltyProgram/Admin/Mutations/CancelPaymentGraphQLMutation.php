<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\LoyaltyProgramPayment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class CancelPaymentGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'cancelPayment',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:loyalty_program_payments,id,status,2'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_PAYMENT_CANCEL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): true
    {
        $payment = LoyaltyProgramPayment::query()->findOrFail($args['id']);
        $payment->cancel();

        return true;
    }
}
