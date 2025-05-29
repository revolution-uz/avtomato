<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentSummaryGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class InstallmentSummaryGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'installmentSummary',
    ];

    private array $_branches;

    public function type(): Type
    {
        return GraphQL::type(InstallmentSummaryGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        $this->_branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_VIEW_STATISTICS);

        return [
            'page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
            'from' => [
                'type' => Type::string(),
                'rules' => 'nullable|date_format:Y-m-d',
            ],
            'to' => [
                'type' => Type::string(),
                'rules' => 'nullable|date_format:Y-m-d|after_or_equal:from',
            ],
            'branches' => [
                'type' => Type::listOf(Type::nonNull(Type::int())),
                'rules' => 'nullable|array',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_VIEW_STATISTICS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): array
    {
        $this->_branches = $args['branches'] ?? $this->_branches;

        $installmentQuery = Installment::query()->with('customer')
            ->when(isset($args['from']), fn ($q) => $q
                ->whereDate('created_at', '>=', $args['from']))
            ->when(isset($args['to']), fn ($q) => $q
                ->whereDate('created_at', '<=', $args['to']))
            ->when($this->_branches, fn ($q) => $q
                ->whereIn('branch_id', $this->_branches));

        return [
            'items' => $installmentQuery->clone()->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1),
            'totals' => $installmentQuery->clone()->select([
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(initial_fee) as total_initial_fee'),
                DB::raw('SUM(' . Installment::getInstallmentAmountRaw(true) . ') as total_installment_amount'),
                DB::raw('SUM(' . Installment::getDebtAmountRaw(true) . ') as total_debt_amount'),
            ])->first(),
        ];
    }

    protected function rules(array $args = []): array
    {
        return [
            'branches.*' => [
                'required',
                'integer',
                Rule::exists(Branch::class, 'id')
                    ->whereNull('deleted_at')
                    ->when($this->_branches, fn ($q) => $q->whereIn('id', $this->_branches)),
            ],
        ];
    }
}
