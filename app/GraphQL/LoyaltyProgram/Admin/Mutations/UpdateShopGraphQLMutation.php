<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\LoyaltyProgramm\ShopStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\ShopGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Shop;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateShopGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateShop',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(Shop::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'name' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'address' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'lat' => [
                'type' => Type::float(),
                'rules' => 'nullable|numeric',
            ],
            'lng' => [
                'type' => Type::float(),
                'rules' => 'nullable|numeric',
            ],
            'image' => [
                'type' => GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Shop $root) => $root->image,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', Rule::in(ShopStatusEnum::cases())],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ShopGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_SHOP_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $shop = Shop::findOrFail($args['id']);
        $shop->fill($args);
        $shop->saveOrFail();

        return $shop->refresh();
    }
}
