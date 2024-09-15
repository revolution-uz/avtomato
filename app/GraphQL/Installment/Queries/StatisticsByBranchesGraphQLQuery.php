<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\StatisticsByBranchesGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Installment\InstallmentService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class StatisticsByBranchesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'StatisticsByBranches',
    ];

    private InstallmentService $installmentService;
    private array $_branches;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
    }

    public function args(): array
    {
        $this->_branches = RbacHelper::getEnabledBranches(PermissionEnum::STATISTICS_BY_BRANCHES);

        return [
            'date' => [
                'name' => 'date',
                'type' => Type::string(),
                'rules' => 'nullable|date_format:Y-m-d',
                'description' => 'Ex: 2024-01-01',
            ],
            'branch_ids' => [
                'type' => Type::listOf(Type::int()),
                'rules' => [
                    'nullable',
                    'array',
                    'exists:installments,branch_id',
                    Rule::exists('branches', 'id')->whereNull('deleted_at')->when($this->_branches, fn ($q) => $q->where('id', $this->_branches)),
                ],
            ],
        ];
    }

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
        $this->_branches = isset($args['branch_id']) ? $args['branch_ids'] : $this->_branches;
        $date = isset($args['date']) ? Carbon::createFromFormat('Y-m-d', $args['date']) : null;

        return $this->installmentService->statisticsByBranches(
            $date,
            $this->_branches,
        );
    }
}
