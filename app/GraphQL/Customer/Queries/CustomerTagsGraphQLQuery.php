<?php

namespace App\GraphQL\Customer\Queries;

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

class CustomerTagsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'customerTags',
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
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_TAG_VIEW);
    }

    public function resolve(): Collection
    {
        return Tag::query()->whereHas('customers')->get();
    }
}
