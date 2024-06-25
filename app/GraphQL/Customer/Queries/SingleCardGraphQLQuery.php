<?php

namespace App\GraphQL\Customer\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Card;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleCardGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleCard',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:cards,id'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Card::query()->findOrFail($args['id']);
    }
}
