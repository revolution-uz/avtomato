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
use Illuminate\Validation\Rule;
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
                'rules' => [
                    'required',
                    Rule::exists(DeliveryPoint::class, 'id')->whereNull('deleted_at'),
                ],
            ],
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
        return RbacHelper::checkPermission(PermissionEnum::DELIVERY_POINT_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): DeliveryPoint
    {
        $model = DeliveryPoint::query()->findOrFail($args['id']);
        $model->update($args['deliveryPoint']);
        $model->refresh();

        return $model;
    }
}
