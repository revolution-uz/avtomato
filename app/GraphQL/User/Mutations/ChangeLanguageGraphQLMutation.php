<?php

namespace App\GraphQL\User\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class ChangeLanguageGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeLanguage',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::string());
    }

    public function args(): array
    {
        return [
            'language' => [
                'name' => 'language',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PROFILE_CHANGE_LANGUAGE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = auth()->user();
        $user->update(['language' => $args['language']]);
        $user->refresh();

        return $user->language;
    }

    protected function rules(array $args = []): array
    {
        return [
            'language' => 'required|string|max:2|in:ru,uz,oz,en',
        ];
    }
}
