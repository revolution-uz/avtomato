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

        return $this->installmentService->getStatisticsForDate($args['date'], $this->_branches);
    }

    protected function rules(array $args = []): array
    {
        return [
            'branches.*' => [
                'required',
                Rule::exists('branches', 'id')
                    ->whereNull('deleted_at')
                    ->when($this->_branches, fn ($q) => $q->whereIn('id', $this->_branches)),
            ],
        ];
    }
}
