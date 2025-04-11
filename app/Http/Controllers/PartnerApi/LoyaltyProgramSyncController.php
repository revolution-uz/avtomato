<?php

namespace App\Http\Controllers\PartnerApi;

use App\DTOs\LoyaltyProgram\ItemDTO;
use App\DTOs\LoyaltyProgram\PriceDTO;
use App\DTOs\LoyaltyProgram\ShopDTO;
use App\DTOs\LoyaltyProgram\StockDTO;
use App\Http\Controllers\Controller;
use App\Jobs\LoyaltyProgram\SyncItemsJob;
use App\Jobs\LoyaltyProgram\SyncPricesJob;
use App\Jobs\LoyaltyProgram\SyncShopsJob;
use App\Jobs\LoyaltyProgram\SyncStocksJob;
use Illuminate\Http\Request;

class LoyaltyProgramSyncController extends Controller
{
    protected $channel = 'loyalty-program';
    protected $type = 'sync-partner';

    public function postShops(Request $request)
    {
        $shops = collect($request->input('shops'))->map(function ($shopData) {
            return ShopDTO::from($shopData);
        });

        foreach ($shops as $shop) {
            SyncShopsJob::dispatch($shop);
        }

        $response = $this->prepareSuccessResponse(['message' => 'Shops are processed successfully']);
        $this->logging($request, $response, $this->channel, $this->type, 'postShops');

        return $this->jsonResponse($response);
    }

    public function postItems(Request $request)
    {
        $items = collect($request->input('items'))->map(function ($itemData) {
            return ItemDTO::from($itemData);
        });

        foreach ($items as $item) {
            SyncItemsJob::dispatch($item);
        }

        $response = $this->prepareSuccessResponse(['message' => 'Items are processed successfully']);
        $this->logging($request, $response, $this->channel, $this->type, 'postItems');

        return $this->jsonResponse($response);
    }

    public function postPrices(Request $request)
    {
        $prices = collect($request->input('prices'))->map(function ($priceData) {
            return PriceDTO::from($priceData);
        });

        foreach ($prices as $item) {
            SyncPricesJob::dispatch($item);
        }

        $response = $this->prepareSuccessResponse(['message' => 'Prices are processed successfully']);
        $this->logging($request, $response, $this->channel, $this->type, 'postPrices');

        return $this->jsonResponse($response);
    }

    public function postStocks(Request $request)
    {
        $stocks = collect($request->input('stocks'))->map(function ($stockData) {
            return StockDTO::from($stockData);
        });

        foreach ($stocks as $stock) {
            SyncStocksJob::dispatch($stock);
        }

        $response = $this->prepareSuccessResponse(['message' => 'Stocks are processed successfully']);
        $this->logging($request, $response, $this->channel, $this->type, 'postStocks');

        return $this->jsonResponse($response);
    }

    public function jsonResponse($response)
    {
        return response()->json($response);
    }

    public function prepareFailedResponse($message)
    {

        return [
            'status' => false, 'result' => null, 'error' => [
                'message' => $message,
            ],
        ];
    }

    private function prepareSuccessResponse($result)
    {
        return ['status' => true, 'result' => $result, 'error' => null];
    }
}
