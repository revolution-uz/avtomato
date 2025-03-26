<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Attachment;
use App\Models\Company\LoyaltyProgram\Shop;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UploadShopImageGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'uploadShopImage',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Shop::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'image' => [
                'type' => Type::nonNull(GraphQL::type('Upload')),
                'rules' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:2048'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_SHOP_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Attachment
    {
        $shop = Shop::query()->findOrFail($args['id']);

        return Attachment::upload($args['image'], AttachmentTypeEnum::LOYALTY_PROGRAM_IMAGE, $shop);
    }
}
