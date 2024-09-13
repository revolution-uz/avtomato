<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\StatisticsGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Installment\InstallmentService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class StatisticsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'statistics',
    ];

    private array $_branches;

    private InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
    }

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
        $this->_branches = isset($args['branch_id']) ? [$args['branch_id']] : $this->_branches;

        return $this->installmentService->getStatistics(
            Carbon::createFromFormat('Y-m-d', $args['from']),
            Carbon::createFromFormat('Y-m-d', $args['to']),
            $this->_branches
        );
    }
}
