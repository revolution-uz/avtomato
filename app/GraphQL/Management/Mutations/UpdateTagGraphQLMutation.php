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

class UpdateTagGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTag',
    ];

    public function args(): array
    {
        return [
            'tag_id' => [
                'name' => 'tag_id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:tags,id'],
            ],
            'tag' => [
                'name' => 'tag',
                'type' => Type::nonNull(GraphQL::type(TagInputGraphQLType::GRAPHQL_TYPE_NAME)),
                'required' => 'array',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TagGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TAG_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $tag = Tag::query()->findOrFail($args['tag_id']);
        $tag->update([
            'name_oz' => $args['tag']['name_oz'],
            'name_en' => $args['tag']['name_en'],
            'name_ru' => $args['tag']['name_ru'],
            'name_uz' => $args['tag']['name_uz'],
        ]);
        $tag->refresh();

        return $tag;
    }
}
