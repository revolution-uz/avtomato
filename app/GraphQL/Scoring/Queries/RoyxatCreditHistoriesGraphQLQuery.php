<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\RoyxatCreditHistory\RoyxatCreditHistoryGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class RoyxatCreditHistoriesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'royxatCreditHistories',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(GraphQL::type(RoyxatCreditHistoryGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'search' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
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
        return RbacHelper::checkPermission(PermissionEnum::SCORING_ROYXAT_CREDIT_HISTORY_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ScoringRequest::query()
            ->where('type', ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY)
            ->when(isset($args['search']), fn (Builder $query) => $query
                ->where('request->passport_id', 'ILIKE', "%{$args['search']}%")
                ->orWhere('request->pinfl', 'ILIKE', "%{$args['search']}%")
            )
            ->when(
                isset($args['sort']),
                fn (Builder|ScoringRequest $query) => $query->orderBySortable($args['sort']),
                fn (Builder|ScoringRequest $query) => $query->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
