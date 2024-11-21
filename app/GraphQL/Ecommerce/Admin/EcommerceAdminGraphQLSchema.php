<?php

namespace App\GraphQL\Ecommerce\Admin;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateBannerGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateDeliveryPointGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateProductBrandGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateProductGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateProductOptionGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateProductOptionGroupGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\CreateProductOptionValueGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\DeleteProductBrandGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\DeleteProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\DeleteProductGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\DeleteProductOptionGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\DeleteProductOptionGroupGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\DeleteProductOptionValueGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UpdateBannerGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UpdateDeliveryPointGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UpdateOrderStatusGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UpdateProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UpdateProductOptionGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UpdateProductOptionGroupGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UpdateProductOptionValueGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UploadImageProductBrandGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UploadImageProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Mutations\UploadImageProductGraphQLMutation;
use App\GraphQL\Ecommerce\Admin\Queries\BannersGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\DeliveryPointsGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\OrdersGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductJoinOptionsGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductOptionGroupsGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductOptionsGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductOptionValuesGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductsGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\ProductVariationsGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleBannerGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleDeliveryPointGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleProductBrandGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleProductCategoryGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleProductGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleProductOptionGroupGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleProductOptionValueGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleProductVariationGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Queries\UserGraphQLQuery;
use App\GraphQL\Ecommerce\Admin\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\BannerInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\DeliveryPointGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\DeliveryPointInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\EcommerceImageGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderItemGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderItemInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderStatusGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderStatusHistoryGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderStatusInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductCategoryInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductJoinOptionInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGroupGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGroupInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductVariationGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductVariationInputGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserCartInputGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\GraphQL\Management\Types\PermissionBranchItemGraphQLType;
use App\GraphQL\Management\Types\PermissionEditableItemGraphQLType;
use App\GraphQL\Management\Types\PermissionGraphQLType;
use App\GraphQL\Management\Types\PermissionGroupGraphQLType;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\GraphQL\Management\Types\TemplateThemeGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceAdminGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                CreateProductCategoryGraphQLMutation::class,
                CreateProductGraphQLMutation::class,
                CreateProductOptionGraphQLMutation::class,
                CreateBannerGraphQLMutation::class,
                CreateProductBrandGraphQLMutation::class,
                CreateProductOptionGroupGraphQLMutation::class,
                CreateProductOptionValueGraphQLMutation::class,
                CreateDeliveryPointGraphQLMutation::class,
                UploadImageProductCategoryGraphQLMutation::class,
                UploadImageProductBrandGraphQLMutation::class,
                UploadImageProductGraphQLMutation::class,
                UpdateProductCategoryGraphQLMutation::class,
                UpdateBannerGraphQLMutation::class,
                UpdateProductOptionGroupGraphQLMutation::class,
                UpdateProductOptionValueGraphQLMutation::class,
                UpdateProductOptionGraphQLMutation::class,
                UpdateOrderStatusGraphQLMutation::class,
                UpdateDeliveryPointGraphQLMutation::class,
                DeleteProductCategoryGraphQLMutation::class,
                DeleteProductOptionGroupGraphQLMutation::class,
                DeleteProductOptionValueGraphQLMutation::class,
                DeleteProductOptionGraphQLMutation::class,
                DeleteProductGraphQLMutation::class,
                DeleteProductBrandGraphQLMutation::class,
            ],
            'query' => [
                ProductCategoriesGraphQLQuery::class,
                ProductsGraphQLQuery::class,
                ProductBrandsGraphQLQuery::class,
                ProductVariationsGraphQLQuery::class,
                ProductOptionGroupsGraphQLQuery::class,
                ProductOptionsGraphQLQuery::class,
                ProductOptionValuesGraphQLQuery::class,
                ProductJoinOptionsGraphQLQuery::class,
                BannersGraphQLQuery::class,
                OrdersGraphQLQuery::class,
                UserGraphQLQuery::class,
                DeliveryPointsGraphQLQuery::class,
                SingleDeliveryPointGraphQLQuery::class,
                SingleProductGraphQLQuery::class,
                SingleProductCategoryGraphQLQuery::class,
                SingleProductOptionValueGraphQLQuery::class,
                SingleProductOptionGroupGraphQLQuery::class,
                SingleProductVariationGraphQLQuery::class,
                SingleProductBrandGraphQLQuery::class,
                SingleBannerGraphQLQuery::class,
                SingleUserGraphQLQuery::class,
            ],
            'types' => [
                EcommerceImageGraphQLType::class,
                ProductCategoryGraphQLType::class,
                ProductCategoryInputGraphQLType::class,
                ProductGraphQLType::class,
                BannerGraphQLType::class,
                BannerInputGraphQLType::class,
                ProductInputGraphQLType::class,
                ProductBrandGraphQLType::class,
                ProductBrandInputGraphQLType::class,
                OrderInputGraphQLType::class,
                //                OrderItemInputGraphQLType::class,
                //                OrderStatusInputGraphQLType::class,
                //                OrderStatusGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductJoinOptionInputGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionValueInputGraphQLType::class,
                ProductOptionGraphQLType::class,
                ProductOptionInputGraphQLType::class,
                ProductVariationInputGraphQLType::class,
                ProductVariationGraphQLType::class,
                UserCartInputGraphQLType::class,
                ProductOptionGroupInputGraphQLType::class,
                ProductOptionGroupGraphQLType::class,
                UserGraphQLType::class,
                OrderGraphQLType::class,
                OrderStatusHistoryGraphQLType::class,
                OrderItemGraphQLType::class,
                AdminGraphQLType::class,
                AttachmentGraphQLType::class,
                TemplateThemeGraphQLType::class,
                AddressGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                RoleGraphQLType::class,
                PermissionGroupGraphQLType::class,
                PermissionGraphQLType::class,
                PermissionEditableItemGraphQLType::class,
                PermissionBranchItemGraphQLType::class,
                DeliveryPointGraphQLType::class,
                DeliveryPointInputGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum', 'is_soft_delete_token', 'tenant_logging'],
        ];
    }
}
