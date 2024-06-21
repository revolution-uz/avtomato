<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\User\UserService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UploadAvatarGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'uploadAvatar',
    ];

    public function args(): array
    {
        return [
            'avatar' => [
                'name' => 'avatar',
                'type' => GraphQL::type('Upload'),
                'rules' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg',
                    'max:10240',
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PROFILE_CHANGE_AVATAR);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = (new UserService)->updateAvatar(auth()->user(), $args['avatar']);

        return $user->avatar;
    }
}
