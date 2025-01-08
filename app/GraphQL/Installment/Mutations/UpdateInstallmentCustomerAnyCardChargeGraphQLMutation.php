<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Services\Installment\InstallmentService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class UpdateInstallmentCustomerAnyCardChargeGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateInstallmentCustomerAnyCardCharge',
    ];

    public function __construct(private InstallmentService $installmentService) {}

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_EDIT);

        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id')->whereNull('deleted_at')->when($branches, fn ($q) => $q->where('branch_id', $branches)),
                ],
            ],
            'any_card_charge' => [
                'name' => 'any_card_charge',
                'type' => Type::nonNull(Type::boolean()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(InstallmentGraphQLType::type());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->installmentService->toggleAutoAndAnyCardCharge(Installment::findOrFail($args['id']), null, $args['any_card_charge']);
    }
}
