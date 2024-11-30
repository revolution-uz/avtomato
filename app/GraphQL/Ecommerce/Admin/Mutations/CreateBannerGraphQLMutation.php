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
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateBannerGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createBanner',
    ];

    public function args(): array
    {
        return [
            'banner' => [
                'name' => 'banner',
                'type' => Type::nonNull(BannerInputGraphQLType::type()),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(BannerGraphQLType::type());
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BANNER_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Banner
    {
        $banner = Banner::create(
            Arr::only(
                $args['banner'],
                ['name_uz', 'name_ru', 'name_en', 'name_oz', 'url', 'is_active', 'category_id', 'brand_id', 'product_id']
            )
        );

        $imagePath = null;

        foreach (['uz', 'ru', 'en', 'oz'] as $locale) {
            if ($locale !== 'image_uz' && ! isset($args['banner']["image_{$locale}"])) {
                continue;
            }

            $imagePath = $banner->uploadImage($args['banner']["image_{$locale}"], $locale, $imagePath);
        }

        return $banner;
    }
}
