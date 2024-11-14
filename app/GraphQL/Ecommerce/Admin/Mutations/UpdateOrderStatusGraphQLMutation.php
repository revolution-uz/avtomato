<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\Order;
use App\Services\Ecommerce\OrderService;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateOrderStatusGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateOrderStatus',
    ];

    public function __construct(readonly OrderService $orderService) {}

    public function args(): array
    {
        return [
            'order_id' => [
                'name' => 'order_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:ecommerce_orders,id'],
            ],
            'status_id' => [
                'name' => 'status_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:ecommerce_order_statuses,id'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ORDER_STATUS_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Order
    {
        return $this->orderService->changeOrderStatus(Order::findOrFail($args['order_id']), $args['status_id']);
    }
}
