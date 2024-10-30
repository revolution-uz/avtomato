<?php

namespace App\GraphQL\Scoring\Mutations;

use App\DTOs\Scoring\UzumNasiya\UzumNasiyaLimitCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Scoring\Types\UzumNasiya\UzumNasiyaLimitCreateInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Scoring\UzumNasiyaService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateUzumNasiyaLimitScoringGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createUzumNasiyaLimitScoring',
    ];

    public function __construct(
        private readonly UzumNasiyaService $uzumNasiyaService,
    ) {}

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'type' => Type::nonNull(GraphQL::type(UzumNasiyaLimitCreateInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::int());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_UZUM_NASIYA_LIMIT_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->uzumNasiyaService->scoring(UzumNasiyaLimitCreateRequestDTO::from($args['data']))->id;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args) + [
            'data.phone' => 'required',
        ];
    }
}
