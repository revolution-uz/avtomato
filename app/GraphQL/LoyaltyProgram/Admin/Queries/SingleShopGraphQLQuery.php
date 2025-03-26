<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\ShopGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Shop;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleShopGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleShop',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ShopGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Shop::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_SHOP_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Shop::query()->findOrFail($args['id']);
    }
}
