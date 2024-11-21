<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\BannerInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\Banner;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateBannerGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateBanner',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:ecommerce_banners,id'],
            ],
            'banner' => [
                'name' => 'banner',
                'type' => Type::nonNull(GraphQL::type(BannerInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(BannerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BANNER_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Banner
    {
        $model = Banner::query()->findOrFail($args['id']);
        $model->update($args['banner']);
        $model->refresh();

        return $model;
    }
}
