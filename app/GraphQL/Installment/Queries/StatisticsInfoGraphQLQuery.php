<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\StatisticsInfoGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Installment\InstallmentService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class StatisticsInfoGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'statisticsInfo',
    ];

    private array $_branches;

    public function __construct(private readonly InstallmentService $installmentService) {}

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

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): array
    {
        $this->_branches = isset($args['branch_id']) ? [$args['branch_id']] : $this->_branches;

        return $this->installmentService->getStatisticsForDate($args['date'], $this->_branches);
    }
}
