<?php

namespace App\GraphQL\Ecommerce;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\GraphQL\Customer\Types\TagGraphQLType;
use App\GraphQL\Ecommerce\Mutations\CreateBannerGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductBrandGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductOptionGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductOptionGroupGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductOptionValueGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\DeleteProductBrandGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\DeleteProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\DeleteProductGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\DeleteProductOptionGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\DeleteProductOptionGroupGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\DeleteProductOptionValueGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UpdateBannerGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UpdateOrderStatusGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UpdateProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UpdateProductOptionGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UpdateProductOptionGroupGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UpdateProductOptionValueGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UploadImageProductBrandGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UploadImageProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\UploadImageProductGraphQLMutation;
use App\GraphQL\Ecommerce\Queries\BannersGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\OrderItemsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\OrdersGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductJoinOptionsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductOptionGroupsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductOptionsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductOptionValuesGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductVariationsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleBannerGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleProductBrandGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleProductCategoryGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleProductGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleProductOptionGroupGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleProductOptionValueGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleProductVariationGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\SingleUserGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\UsersGraphQLQuery;
use App\GraphQL\Ecommerce\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Types\BannerInputGraphQLType;
use App\GraphQL\Ecommerce\Types\EcommerceImageGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderInputGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderItemGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderItemInputGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderStatusGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderStatusInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductJoinOptionInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGroupGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGroupInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductVariationGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductVariationInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserCartInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserFavoriteInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserGraphQLType;
use App\GraphQL\Installment\Types\InstallmentCommentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\GraphQL\Installment\Types\InstallmentGuarantorGraphQLType;
use App\GraphQL\Installment\Types\TariffGraphQLType;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\GraphQL\Installment\Types\TransactionTypeGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\GraphQL\Scoring\Types\Card\CardScoringGraphQLType;
use App\GraphQL\Scoring\Types\Debt\DebtScoringGraphQLType;
use App\GraphQL\Scoring\Types\Katm\CreditHistory\CreditHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\InpsHistory\InpsHistoryGraphQLType;
use App\GraphQL\Scoring\Types\Katm\PensionHistory\PensionInfoGraphQLType;
use App\GraphQL\Scoring\Types\ScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\ScoringRequestUnionGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceGraphQLSchema implements ConfigConvertible
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
                UploadImageProductCategoryGraphQLMutation::class,
                UploadImageProductBrandGraphQLMutation::class,
                UploadImageProductGraphQLMutation::class,
                UpdateProductCategoryGraphQLMutation::class,
                UpdateBannerGraphQLMutation::class,
                UpdateProductOptionGroupGraphQLMutation::class,
                UpdateProductOptionValueGraphQLMutation::class,
                UpdateProductOptionGraphQLMutation::class,
                UpdateOrderStatusGraphQLMutation::class,
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
                OrderItemsGraphQLQuery::class,
                UsersGraphQLQuery::class,
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
                AttachmentGraphQLType::class,
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
                OrderItemInputGraphQLType::class,
                OrderStatusInputGraphQLType::class,
                OrderStatusGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductJoinOptionInputGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionValueInputGraphQLType::class,
                ProductOptionGraphQLType::class,
                ProductOptionInputGraphQLType::class,
                ProductVariationInputGraphQLType::class,
                ProductVariationGraphQLType::class,
                UserCartInputGraphQLType::class,
                UserFavoriteInputGraphQLType::class,
                ProductOptionGroupInputGraphQLType::class,
                ProductOptionGroupGraphQLType::class,
                UserGraphQLType::class,
                OrderGraphQLType::class,
                OrderItemGraphQLType::class,
                CreditHistoryGraphQLType::class,
                InpsHistoryGraphQLType::class,
                PensionInfoGraphQLType::class,
                CardScoringGraphQLType::class,
                DebtScoringGraphQLType::class,
                InstallmentGraphQLType::class,
                InstallmentGraphGraphQLType::class,
                TransactionGraphQLType::class,
                InstallmentGuarantorGraphQLType::class,
                InstallmentCommentGraphQLType::class,
                AttachmentGraphQLType::class,
                BranchGraphQLType::class,
                AdminGraphQLType::class,
                TariffGraphQLType::class,
                CustomerGraphQLType::class,
                CardGraphQLType::class,
                AddressGraphQLType::class,
                PhoneGraphQLType::class,
                ScoringRequestGraphQLType::class,
                ScoringRequestUnionGraphQLType::class,
                TagGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                TransactionTypeGraphQLType::class,
                RoleGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum', 'is_soft_delete_token', 'tenant_logging'],
        ];
    }
}
