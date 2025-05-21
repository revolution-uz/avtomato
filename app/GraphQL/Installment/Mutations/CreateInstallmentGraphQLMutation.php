<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Feature;
use App\Services\Installment\InstallmentService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateInstallmentGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createInstallment',
    ];

    public function __construct(
        private readonly InstallmentService $installmentService,
    ) {}

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_CREATE);

        return [
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('customers', 'id')->whereNull('deleted_at'),
                ],
            ],
            'branch_id' => [
                'name' => 'branch_id',
                'type' => Type::int(),
                'rules' => [
                    'int',
                    new RequiredIf(Feature::isFeatureActive(FeatureEnum::BRANCH_MANAGEMENT)),
                    Rule::exists('branches', 'id')->whereNull('deleted_at')->when($branches, fn ($q) => $q->where('id', $branches)),
                ],
            ],
            'installment' => [
                'name' => 'installment',
                'type' => Type::nonNull(GraphQL::type(InstallmentInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->installmentService->create($args['installment'] + Arr::except($args, ['installment']));
    }
}
