<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\InpsHistoryGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class InpsHistoriesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'inpsHistories',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(GraphQL::type(InpsHistoryGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'sort' => [
                'name' => 'sort',
                'type' => Type::string(),
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
            ],
            'per_page' => [
                'name' => 'per_page',
                'type' => Type::int(),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_INPS_HISTORY_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ScoringRequest::query()
            ->where('type', ScoringRequestTypeEnum::KATM_INPS_HISTORY)
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('request->pinfl', 'ILIKE', $search)
                        ->orWhere('request->first_name', 'ILIKE', $search)
                        ->orWhere('request->last_name', 'ILIKE', $search)
                        ->orWhere('request->middle_name', 'ILIKE', $search)
                        ->orWhere('request->passport_series', 'ILIKE', $search)
                        ->orWhere('request->passport_number', 'ILIKE', $search);
                });
            })
            ->when(! isset($args['sort']), fn (Builder|ScoringRequest $q) => $q->orderByDesc('id'))
            ->when(isset($args['sort']), fn (Builder|ScoringRequest $q) => $q->orderBySortable($args['sort']))
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }

    protected function rules(array $args = []): array
    {
        return [
            'sort' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|int|min:1',
            'per_page' => 'nullable|int|min:5|max:100',
        ];
    }
}
