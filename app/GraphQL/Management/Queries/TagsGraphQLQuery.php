<?php

namespace App\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\TagGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Tag;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TagsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'tags',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(GraphQL::type(TagGraphQLType::GRAPHQL_TYPE_NAME)));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TAG_VIEW_ALL);
    }

    public function resolve(): Collection
    {
        return Tag::query()->latest()->get();
    }
}
