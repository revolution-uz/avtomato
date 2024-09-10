<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\StatisticsGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class StatisticsGraphQLQuery extends Query
{
    private array $_branches;

    protected $attributes = [
        'name' => 'statistics',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(StatisticsGraphQLType::GRAPHQL_TYPE_NAME))));
    }

    public function args(): array
    {
        $this->_branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_VIEW_STATISTICS);

        return [
            'from' => [
                'name' => 'from',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d',
            ],
            'to' => [
                'name' => 'to',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d|after_or_equal:from',
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

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): array
    {
        $from = Carbon::createFromFormat('Y-m-d', $args['from'])->setTime(0, 0);
        $to = Carbon::createFromFormat('Y-m-d', $args['to'])->setTime(0, 0);

        $data = [];

        for ($date = $from; $date->lte($to); $date->addDay()) {
            $debt = Installment::whereNextPayDate('=', $date)
                ->whereIn('status', [InstallmentStatusEnum::ACTIVE, InstallmentStatusEnum::CLOSED])
                ->selectRaw("
                    SUM(
                        (amount - initial_fee) * (percent/100 + 1) / period * LEAST(
                            GREATEST(
                                CEIL(DATE_PART('month', AGE(?, starts_in)) + 1), 0
                            ),
                            period
                        ) - paid_amount
                    ) as debt
                ", [$date])
                ->when(isset($args['branch_id']), fn ($q) => $q->where('branch_id', $args['branch_id']))
                ->when(! isset($args['branch_id']) and $this->_branches, fn ($q) => $q->whereIn('branch_id', $this->_branches))
                ->value('debt') ?? 0;

            $paid = Transaction::query()->whereDate('paid_at', $date)
                ->when(isset($args['branch_id']), fn ($q) => $q->where('branch_id', $args['branch_id']))
                ->when(! isset($args['branch_id']) and $this->_branches, fn ($q) => $q->whereIn('branch_id', $this->_branches))
                ->sum('amount');

            $data[] = [
                'date' => clone $date,
                'debt' => round($debt, 2),
                'paid' => round($paid, 2),
            ];
        }

        return $data;
    }
}
