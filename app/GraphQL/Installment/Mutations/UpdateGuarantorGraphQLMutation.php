<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGuarantorGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
use App\Services\Installment\InstallmentService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateGuarantorGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateGuarantor',
    ];

    private InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
    }

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_UPDATE_GUARANTOR);

        return [
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id')->whereNull('deleted_at')->when($branches, fn ($q) => $q->whereIn('branch_id', $branches)),
                ],
            ],
            'guarantor_id' => [
                'name' => 'guarantor_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installment_guarantors', 'customer_id'),
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
        return Type::nonNull(GraphQL::type(InstallmentGuarantorGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPDATE_GUARANTOR);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->installmentService->toggleGuarantorAutoCharge(InstallmentGuarantor::query()
            ->where('installment_id', $args['installment_id'])
            ->where('customer_id', $args['guarantor_id'])
            ->firstOrFail(), $args['any_card_charge']);
    }
}
