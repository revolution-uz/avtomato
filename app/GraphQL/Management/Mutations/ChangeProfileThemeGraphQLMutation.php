<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\TemplateThemeGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\User\UserService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ChangeProfileThemeGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeProfileTheme',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TemplateThemeGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'layout_name' => [
                'name' => 'layout_name',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PROFILE_CHANGE_THEME);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = (new UserService)->updateTheme(auth()->user(), $args);

        return $user->template_theme;
    }

    protected function rules(array $args = []): array
    {
        return [
            'layout_name' => ['required', 'string'],
        ];
    }
}
