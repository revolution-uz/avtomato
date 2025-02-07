<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class DeleteBranchGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteBranch',
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
        return RbacHelper::checkPermission(PermissionEnum::BRANCH_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        Branch::query()->whereIn('id', $args['ids'])->delete();

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
                Rule::exists(Branch::class, 'id')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    if (Installment::query()->where('branch_id', $value)->exists()) {
                        $fail(__('api.cannot_delete', ['item' => __('api.Branch'), 'element' => __('api.Installment')]));

                        return;
                    }
                    if (Transaction::query()->where('branch_id', $value)->exists()) {
                        $fail(__('api.cannot_delete', ['item' => __('api.Branch'), 'element' => __('api.Transaction')]));
                    }
                },
            ],
        ];
    }
}
