<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\PensionHistory\PensionInfoGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ReadPensionInfoGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'readKatmCreditHistory',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(PensionInfoGraphQLType::GRAPHQL_TYPE_NAME);
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
        return RbacHelper::checkPermission(PermissionEnum::SCORING_PENSION_INFO_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $scoring = ScoringRequest::findOrFail($args['id']);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::KATM_PENSION_INFO) {
            throw new ModelNotFoundException;
        }

        return $scoring;
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => 'int|exists:scoring_requests,id',
        ];
    }
}
