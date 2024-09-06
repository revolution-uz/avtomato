<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\TagGraphQLType;
use App\GraphQL\Customer\Types\TagInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Tag;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
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
            'tags' => [
                'name' => 'tags',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(TagInputGraphQLType::GRAPHQL_TYPE_NAME)))),
                'required' => 'array',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(GraphQL::type(TagGraphQLType::GRAPHQL_TYPE_NAME)));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_TAG_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        DB::beginTransaction();

        try {
            $tagsData = array_map(fn ($tagData) => [
                'name_uz' => $tagData['name_uz'],
                'name_ru' => $tagData['name_ru'],
                'name_en' => $tagData['name_en'],
                'name_oz' => $tagData['name_oz'],
            ], $args['tags']);

            // Insert all tags at once
            Tag::query()->insert($tagsData);

            $createdTags = Tag::query()->latest()->get();

            DB::commit();

            return $createdTags;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
