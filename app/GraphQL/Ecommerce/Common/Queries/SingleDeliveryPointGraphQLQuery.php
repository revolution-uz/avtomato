<?php

namespace App\GraphQL\Ecommerce\Common\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\DeliveryPointGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\DeliveryPoint;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleDeliveryPointGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleDeliveryPoint',
    ];

    public function type(): Type
    {
        return GraphQL::type(DeliveryPointGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(DeliveryPoint::class, 'id')
                        ->where(function ($query) {
                            $query->whereNull('deleted_at');
                        }),
                ],
            ],
        ];
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::DELIVERY_POINT_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return DeliveryPoint::query()->findOrFail($args['id']);
    }
}
