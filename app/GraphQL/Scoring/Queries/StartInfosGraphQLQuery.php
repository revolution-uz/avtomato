<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Katm\StartInfo\StartInfoGraphQLType;
use App\Helpers\RbacHelper;
use App\Jobs\UpdateScoringRequestStatusJob;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class StartInfosGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'startInfos',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(GraphQL::type(StartInfoGraphQLType::GRAPHQL_TYPE_NAME));
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
        return RbacHelper::checkPermission(PermissionEnum::SCORING_START_INFO_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        foreach (
            ScoringRequest::query()
                ->where('status', ScoringRequestStatusEnum::WAITING)
                ->where('type', ScoringRequestTypeEnum::KATM_START_INFO)
                ->select('id')->get() as $item
        ) {
            dispatch(new UpdateScoringRequestStatusJob($item->id))->onQueue('scoring');
        }

        return ScoringRequest::query()
            ->where('type', ScoringRequestTypeEnum::KATM_START_INFO)
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
            ->when(
                isset($args['sort']),
                fn (Builder|ScoringRequest $q) => $q->orderBySortable($args['sort']),
                fn (Builder|ScoringRequest $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
