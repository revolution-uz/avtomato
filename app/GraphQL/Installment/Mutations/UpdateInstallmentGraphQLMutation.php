<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateInstallmentGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateInstallment',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id')->whereNull('deleted_at')->where('status', InstallmentStatusEnum::DRAFT),
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
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $installment = Installment::findOrFail($args['id']);

        if ($installment->status == InstallmentStatusEnum::DRAFT) {
            $installment->updateOrFail($args['installment']);
        }

        return $installment;
    }
}
