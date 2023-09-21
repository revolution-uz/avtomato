<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\CalculatorRequest;
use App\Http\Resources\InstallmentPaymentResource;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;

class InstallmentController extends Controller
{
    public function calculator(CalculatorRequest $request): JsonResponse
    {
        $percent = $request->getTariff()->percent;
        $amount = $request->amount;
        $amount -= $request->initial_amount;
        $portion = $amount * $percent / 100;
        $currentDate = Carbon::now();
        $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($request->period - 1));
        $calculated = [];
        $i = 1;
        foreach ($periods as $period) {
            $calculated[] = [
                'id' => $i,
                'date' => $period->format("Y-m-d"),
                'amount' => $amount / $request->period,
                'portion' => $portion,
                'monthly' => $amount / $request->period + $portion,
            ];
            $i++;
        }
        return ApiResponse::success($calculated);
    }
}
