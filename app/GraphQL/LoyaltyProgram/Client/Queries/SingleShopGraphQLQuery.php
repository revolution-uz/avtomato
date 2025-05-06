<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Admin\Types\ShopGraphQLType;
use App\Models\Company\LoyaltyProgram\Shop;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
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
                'rules' => ['required', 'int', 'exists:loyalty_program_shops,id,deleted_at,NULL'],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Shop::query()->findOrFail($args['id']);
    }
}
