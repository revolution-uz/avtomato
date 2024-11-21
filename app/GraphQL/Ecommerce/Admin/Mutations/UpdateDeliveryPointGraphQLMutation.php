<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\DeliveryPointGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\DeliveryPointInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\DeliveryPoint;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateDeliveryPointGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateDeliveryPoint',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:ecommerce_delivery_points,id'],
            ],
            'delivery_point' => [
                'name' => 'delivery_point',
                'type' => Type::nonNull(GraphQL::type(DeliveryPointInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(DeliveryPointGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::DELIVERY_POINT_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): DeliveryPoint
    {
        $model = DeliveryPoint::query()->findOrFail($args['id']);
        $model->update($args['delivery_point']);
        $model->refresh();

        return $model;
    }
}
