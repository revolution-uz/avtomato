<?php

namespace App\GraphQL\Info\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Info\Types\MahallaGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Mahalla;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class MahallasGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'mahallas',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INFO_MAHALLAS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Mahalla::query()->get();
    }
}
