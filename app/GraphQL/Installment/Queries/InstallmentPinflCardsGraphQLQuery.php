<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\UniredCardGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\CentralUniredCard;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class InstallmentPinflCardsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'installmentPinflCards',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(UniredCardGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_VIEW);

        return [
            'installment_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Installment::class, 'id')->when($branches, fn ($q) => $q->whereIn('branch_id', $branches))->whereNull('deleted_at'),
                ],
            ],
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $installment = Installment::query()->findOrFail($args['installment_id']);
        $customerPinfls = $installment->guarantors->pluck('pinfl')->prepend($installment->customer->pinfl)->toArray();

        return CentralUniredCard::query()
            ->whereHas('customer', fn (Builder $q) => $q->whereIn('pinfl', $customerPinfls))
            ->when(
                isset($args['sort']),
                fn (Builder $q) => $q->orderBySortable($args['sort']),
                fn (Builder $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
