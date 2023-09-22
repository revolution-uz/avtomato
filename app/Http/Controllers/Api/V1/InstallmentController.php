<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\CalculatorRequest;
use App\Http\Resources\InstallmentPaymentResource;
use App\Models\Company\Tariffs;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class InstallmentController extends Controller
{

    public function calculator(CalculatorRequest $request): JsonResponse
    {
        return ApiResponse::success($this->prepareIntallmentGraph($request));
    }

    public function exportCalculator(string $type, CalculatorRequest $request)
    {
        $data = [["ID", "Date", "Amount", "Portion", "Monthly"]];
        foreach ($this->prepareIntallmentGraph($request) as $item)
            $data[] = [$item->id, $item->date, $item->amount, $item->portion, $item->monthly];
        $collection = collect($data);
        $csv = $collection->reduce(function ($carry, $row) {
            return $carry . implode(',', $row) . PHP_EOL;
        }, '');
        $response = new Response($csv);

        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="calculator.csv"');

        return $response;
    }

    private function prepareIntallmentGraph(CalculatorRequest $request)
    {
        $tariff = Tariffs::query()->where('id', $request->tariff_id)->first();
        $amount = $request->amount;
        $amount -= $request->initial_amount;
        $portion = $amount * $tariff->percent / 100;

        $currentDate = Carbon::now();
        $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($request->period - 1));

        $calculated = [];
        $i = 1;
        foreach ($periods as $period) {
            $calculated[] = new InstallmentPaymentResource(
                $i,
                $period->format("Y-m-d"),
                $amount / $request->period,
                $portion,
                $amount / $request->period + $portion
            );
            $i++;
        }
        return $calculated;
    }
}
