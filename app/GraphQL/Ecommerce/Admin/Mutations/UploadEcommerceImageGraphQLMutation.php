<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\EcommerceImageGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\EcommerceImage;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UploadEcommerceImageGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'uploadEcommerceImage',
    ];

    public function args(): array
    {
        return [
            'image' => [
                'name' => 'image',
                'type' => GraphQL::type('Upload'),
                'rules' => 'required|file|mimes:jpeg,png,jpg|max:1024',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(EcommerceImageGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ECOMMERCE_IMAGE_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return EcommerceImage::upload($args['image']);
    }
}
