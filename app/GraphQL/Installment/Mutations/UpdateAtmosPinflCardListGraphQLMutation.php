<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Services\AtmosService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class UpdateAtmosPinflCardListGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateAtmosPinflCardList',
    ];

    public function __construct(readonly AtmosService $atmosService) {}

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_UPDATE_ATMOS_PINFL_CARD_LIST);

        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('customers', 'id')->whereNull('deleted_at')->when($branches, fn ($q) => $q->whereIn('branch_id', $branches)),
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
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPDATE_ATMOS_PINFL_CARD_LIST);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->atmosService->updatePinflCardsList($args['customer_id']);
    }
}
