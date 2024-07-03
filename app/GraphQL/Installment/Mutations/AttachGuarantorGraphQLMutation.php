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

class AttachGuarantorGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachGuarantor',
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
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(InstallmentGuarantorGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_ATTACH_GUARANTOR);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $attributes = $args;
        $attributes['any_card_charge'] = false;

        return InstallmentGuarantor::create($attributes);
    }

    protected function rules(array $args = []): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('customers', 'id'),
                Rule::unique('installment_guarantors', 'customer_id')
                    ->where('installment_id', $args['installment_id']),
            ],
        ];
    }
}
