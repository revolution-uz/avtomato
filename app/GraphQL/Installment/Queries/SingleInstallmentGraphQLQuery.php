<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleInstallmentGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleInstallment',
    ];

    public function type(): Type
    {
        return GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_VIEW);

        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists('installments', 'id')->whereNull('deleted_at')->when($branches, fn ($q) => $q->where('branch_id', $branches)),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Installment::query()->findOrFail($args['id']);
    }
}
