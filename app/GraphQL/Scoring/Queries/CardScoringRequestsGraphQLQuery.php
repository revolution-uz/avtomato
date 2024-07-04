<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Card\CardScoringGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CardScoringRequestsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'cardScoringRequests',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(GraphQL::type(CardScoringGraphQLType::GRAPHQL_TYPE_NAME));
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
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::in([
                        ScoringRequestTypeEnum::HUMO->value,
                        ScoringRequestTypeEnum::UZCARD->value,
                    ]),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $type = ScoringRequestTypeEnum::from($args['type']);

        return $type === ScoringRequestTypeEnum::HUMO
            ? RbacHelper::checkPermission(PermissionEnum::SCORING_HUMO_VIEW_ALL)
            : RbacHelper::checkPermission(PermissionEnum::SCORING_UZCARD_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ScoringRequest::query()
            ->where('type', $args['type'])
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('request->number', 'ILIKE', $search)
                        ->orWhere('request->expiry', 'ILIKE', $search);
                });
            })
            ->when(! isset($args['sort']), fn (Builder|ScoringRequest $q) => $q->orderByDesc('id'))
            ->when(isset($args['sort']), fn (Builder|ScoringRequest $q) => $q->orderBySortable($args['sort']))
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
