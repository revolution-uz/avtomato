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
        return RbacHelper::checkPermission(PermissionEnum::TAG_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        DB::beginTransaction();

        try {
            // Insert all tags at once
            Tag::query()->create([
                'name_oz' => $args['tags'][0]['name_oz'],
                'name_en' => $args['tags'][0]['name_en'],
                'name_ru' => $args['tags'][0]['name_ru'],
                'name_uz' => $args['tags'][0]['name_uz'],
            ]);

            $createdTags = Tag::query()->orderByDesc('id')->get();

            DB::commit();

            return $createdTags;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
