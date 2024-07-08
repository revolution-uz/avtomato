<?php

namespace App\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteInstallmentGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteInstallment',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): true
    {
        Installment::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! Installment::query()->where('id', $value)->where('status', InstallmentStatusEnum::DRAFT)->exists()) {
                        $fail(__('api.not found'));
                    }
                },
            ],
        ];
    }
}
