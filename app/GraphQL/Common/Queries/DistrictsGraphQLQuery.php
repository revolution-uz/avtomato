<?php

namespace App\GraphQL\Common\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\District;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class DistrictsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'districts',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INFO_DISTRICTS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return District::query()->get();
    }
}
