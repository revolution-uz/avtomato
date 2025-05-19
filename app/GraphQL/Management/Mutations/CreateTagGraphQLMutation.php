<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\TagGraphQLType;
use App\GraphQL\Customer\Types\TagInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Tag;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateTagGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTag',
    ];

    public function args(): array
    {
        return [
            'tag' => [
                'name' => 'tag',
                'type' => Type::nonNull(GraphQL::type(TagInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TagGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TAG_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $tag = new Tag;
        $tag->fill($args['tag']);
        $tag->save();

        return $tag;
    }
}
