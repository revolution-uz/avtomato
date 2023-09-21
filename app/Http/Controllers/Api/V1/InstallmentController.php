<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CalculatorHelper;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\CalculatorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class InstallmentController extends Controller
{
    public function __construct(private readonly CalculatorHelper $calculatorHelper)
    {
    }

    public function calculator(CalculatorRequest $request): JsonResponse
    {
        return ApiResponse::success($this->calculatorHelper->prepareIntallmentGraph($request));
    }

    public function exportCalculator(string $type, CalculatorRequest $request)
    {
        $collection = collect($this->calculatorHelper->prepareIntallmentGraphCSV($request));
        $csv = $collection->reduce(function ($carry, $row) {
            return $carry . implode(',', $row) . PHP_EOL;
        }, '');
        $response = new Response($csv);

        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="calculator.csv"');

        return $response;
    }
}
