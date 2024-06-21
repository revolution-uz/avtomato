<?php

namespace App\GraphQL\Info\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Info\Types\RegionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Region;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class RegionsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'regions',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INFO_REGIONS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Region::query()->get();
    }
}
