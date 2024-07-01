<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\GraphQL\Installment\Types\InstallmentGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Feature;
use App\Services\Installment\InstallmentService;
use App\Services\User\BalanceService;
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

    public function args(): array
    {
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
                    new RequiredIf(Feature::isFeatureActive(FeatureEnum::BRANCH_MANAGEMENT)),
                    'int',
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
        return Type::nonNull(GraphQL::type(InstallmentGraphGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_CREATE) && (new BalanceService)->isBalanceEnoughForFeature(PermissionEnum::INSTALLMENT_CREATE->getMyFeature());
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return (new InstallmentService(new BalanceService))->create($args['installment'] + Arr::except($args, ['installments']));
    }
}
