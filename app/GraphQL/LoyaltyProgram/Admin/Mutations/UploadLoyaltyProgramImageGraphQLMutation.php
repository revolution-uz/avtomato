<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\LoyaltyProgramImage;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UploadLoyaltyProgramImageGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'uploadLoyaltyProgramImage',
    ];

    public function args(): array
    {
        return [
            'image' => [
                'type' => GraphQL::type('Upload'),
                'rules' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:1024'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(LoyaltyProgramImageGraphQLType::type());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_IMAGE_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return LoyaltyProgramImage::upload($args['image']);
    }
}
