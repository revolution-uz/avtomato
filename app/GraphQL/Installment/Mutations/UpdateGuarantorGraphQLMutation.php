<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGuarantorGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
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

    public function args(): array
    {
        return [
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installments', 'id')->whereNull('deleted_at'),
                ],
            ],
            'guarantor_id' => [
                'name' => 'guarantor_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists('installment_guarantors', 'id'),
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
        $installmentGuarantor = InstallmentGuarantor::query()
            ->where('installment_id', $args['installment_id'])
            ->where('customer_id', $args['guarantor_id'])
            ->firstOrFail();
        $installmentGuarantor->updateOrFail(['any_card_charge' => $args['any_card_charge']]);

        return $installmentGuarantor;
    }
}
