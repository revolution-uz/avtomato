<?php

namespace App\Services\LoyaltyProgram;

use App\DTOs\LoyaltyProgram\ItemDTO;
use App\DTOs\LoyaltyProgram\PriceDTO;
use App\DTOs\LoyaltyProgram\ShopDTO;
use App\DTOs\LoyaltyProgram\StockDTO;
use App\Enums\Company\LoyaltyProgram\ShopStatusEnum;
use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Price;
use App\Models\Company\LoyaltyProgram\Shop;
use App\Models\Company\LoyaltyProgram\Stock;

class LoyaltyProgramSyncService
{
    public function createOrUpdateShop(ShopDTO $shopDTO)
    {
        if (! ($shopModel = Shop::query()->where('external_code', $shopDTO->code)->first())) {
            $shopModel = new Shop;
            $shopModel->status = ShopStatusEnum::WAITING;
            $shopModel->external_code = $shopDTO->code;
        }
        $shopModel->external_name = $shopDTO->name;
        $shopModel->save();
    }

    public function createOrUpdateItem(ItemDTO $itemDTO)
    {
        if (! ($itemModel = Item::query()->where('external_code', $itemDTO->code)->first())) {
            $itemModel = new Item;
            $itemModel->external_code = $itemDTO->code;
        }

        $itemModel->external_art = $itemDTO->art;
        $itemModel->external_name = $itemDTO->name;
        $itemModel->external_mxik = $itemDTO->mxik;
        $itemModel->external_package_code = $itemDTO->packageCode;
        $itemModel->external_gtin = $itemDTO->gtin;
        $itemModel->save();
    }

    public function createOrUpdatePrice(PriceDTO $itemDTO)
    {
        if (! ($priceModel = Price::query()->where('external_item_code', $itemDTO->item_code)->first())) {
            $priceModel = new Price;
            $priceModel->external_item_code = $itemDTO->item_code;
        }
        $priceModel->external_shop_code = $itemDTO->shop_id;
        $priceModel->external_price = $itemDTO->price;
        $priceModel->save();
    }

    public function createOrUpdateStock(StockDTO $stockDTO)
    {
        if (! ($priceModel = Stock::query()->where('external_item_code', $stockDTO->item_code)->first())) {
            $priceModel = new Stock;
            $priceModel->external_item_code = $stockDTO->item_code;
        }
        $priceModel->external_shop_code = $stockDTO->shop_id;
        $priceModel->external_quantity = $stockDTO->qty;
        $priceModel->save();
    }
}
