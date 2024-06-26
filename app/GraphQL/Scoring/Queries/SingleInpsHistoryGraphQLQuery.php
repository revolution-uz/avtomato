<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\InpsHistoryGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleInpsHistoryGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleInpsHistory',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(InpsHistoryGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_INPS_HISTORY_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ScoringRequest::findOrFail($args['id']);
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                Rule::exists('scoring_requests', 'id')
                    ->where('status', ScoringRequestStatusEnum::SUCCESS)
                    ->where('type', ScoringRequestTypeEnum::KATM_INPS_HISTORY),
            ],
        ];
    }
}
