<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\StatisticsInfoGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class StatisticsInfoGraphQLQuery extends Query
{
    private array $_branches;

    protected $attributes = [
        'name' => 'statisticsInfo',
    ];

    public function type(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(StatisticsInfoGraphQLType::GRAPHQL_TYPE_NAME)));
    }

    public function args(): array
    {
        $this->_branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_VIEW_STATISTICS);

        return [
            'date' => [
                'name' => 'date',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d',
            ],
            'branch_id' => [
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'integer',
                    Rule::exists('branches', 'id')->whereNull('deleted_at')->when($this->_branches, fn ($q) => $q->where('branch_id', $this->_branches)),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_VIEW_STATISTICS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): \Illuminate\Database\Eloquent\Collection
    {
        return Installment::query()
            ->with('transactions', fn ($q) => $q->whereDate('paid_at', $args['date'])->orderByDesc('id')->first())
            ->withSum(['transactions as transactions_sum_amount' => fn ($q) => $q->whereDate('paid_at', $args['date'])], 'amount')
            ->whereIn('status', [InstallmentStatusEnum::ACTIVE, InstallmentStatusEnum::CLOSED])
            ->whereRaw("date_trunc('day', starts_in) + (interval '1 month' * EXTRACT(MONTH FROM AGE('{$args['date']}', starts_in))) = '{$args['date']}'")
            ->whereRaw("EXTRACT(MONTH FROM AGE('{$args['date']}', starts_in)) < period")
            ->when(isset($args['branch_id']), fn ($q) => $q->where('branch_id', $args['branch_id']))
            ->when(! isset($args['branch_id']) and $this->_branches, fn ($q) => $q->whereIn('branch_id', $this->_branches))
            ->orderBy('id')
            ->get();
    }
}
