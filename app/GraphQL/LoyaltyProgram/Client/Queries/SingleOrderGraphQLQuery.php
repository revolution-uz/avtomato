<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Client\Types\OrderGraphQLType;
use App\Models\Company\LoyaltyProgram\Order;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleOrderGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleOrder',
    ];

    public function type(): Type
    {
        return GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Order::class, 'id')
                        ->where('user_id', auth('loyalty-program-client')->id())
                        ->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Order::query()->findOrFail($args['id']);
    }
}
