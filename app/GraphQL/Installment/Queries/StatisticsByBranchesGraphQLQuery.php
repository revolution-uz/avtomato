<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\StatisticsByBranchesGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class StatisticsByBranchesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'StatisticsByBranches',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(StatisticsByBranchesGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::STATISTICS_BY_BRANCHES);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $branches = Installment::query()
            ->select('branch_id', 'type', DB::raw('SUM(paid_amount) as total_amount'))
            ->groupBy('branch_id', 'type')
            ->orderBy('branch_id')
            ->get();
        // Initialize arrays to hold the formatted data and totals
        $statistics = [];
        $totalAmountByType = [];
        $fullTotalAmount = 0;

        // Loop through the results to build the array structure
        foreach ($branches as $branch) {
            /** @var Installment $branch */
            // Check if the branch already exists in the $statistics array
            if (! isset($statistics[$branch->branch_id])) {
                $statistics[$branch->branch_id] = [
                    'branch_id' => $branch->branch_id,
                    'branch_name' => $branch->branch->name,
                    'types' => [],
                    'total_amount_by_branch' => 0,
                ];
            }

            // Add the total amount for each type under the branch
            $statistics[$branch->branch_id]['types'][$branch->type->value] = $branch->total_amount;

            // Increment the branch's total
            $statistics[$branch->branch_id]['total_amount_by_branch'] += $branch->total_amount;

            // Increment the total amount for each type
            if (! isset($totalAmountByType[$branch->type->value])) {
                $totalAmountByType[$branch->type->value] = 0;
            }
            $totalAmountByType[$branch->type->value] += $branch->total_amount;

            // Add to the full total amount across all branches
            $fullTotalAmount += $branch->total_amount;
        }

        // Convert the $statistics array to an indexed array
        $statistics = array_values($statistics);

        // Return the formatted result
        return [
            'branches' => $statistics,
            'total_amount_by_type' => $totalAmountByType,
            'full_total_amount' => $fullTotalAmount,
        ];
    }
}
