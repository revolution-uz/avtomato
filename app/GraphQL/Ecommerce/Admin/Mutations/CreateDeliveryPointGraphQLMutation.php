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
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateDeliveryPointGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createDeliveryPoint',
    ];

    public function args(): array
    {
        return [
            'deliveryPoint' => [
                'name' => 'deliveryPoint',
                'type' => Type::nonNull(DeliveryPointInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(DeliveryPointGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::DELIVERY_POINT_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): DeliveryPoint
    {
        $model = new DeliveryPoint;
        $model->fill($args['deliveryPoint']);
        $model->saveOrFail();

        return $model;
    }
}
