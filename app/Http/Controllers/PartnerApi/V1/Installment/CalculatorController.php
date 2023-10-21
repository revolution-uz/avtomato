<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Installment\CalculatorExportRequest;
use App\Http\Requests\Installment\CalculatorRequest;
use App\Http\Resources\Installment\InstallmentPaymentResource;
use App\Models\Company\Tariff;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class CalculatorController extends Controller
{
    #[OA\Post(
        path: '/api/v1/installment/calculator/calculate',
        description: 'Installment calculator route',
        requestBody: new OA\RequestBody(
            description: 'Calculator request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CalculatorRequest')
        ),
        tags: ['Calculator'],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'InstallmentPaymentResource'),
            new OpenApiNotFoundResponse(message: 'Tariff not found'),
        ]
    )]
    public function calculate(CalculatorRequest $request): JsonResponse
    {
        return ApiResponse::success($this->prepareInstallmentGraph($request));
    }

    #[OA\Post(
        path: '/api/v1/installment/calculator/export/csv',
        description: 'Export installment calculator route',
        requestBody: new OA\RequestBody(
            description: 'Calculator request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CalculatorRequest')
        ),
        tags: ['Calculator'],
        responses: [
            new OA\Response(response: 200, description: 'Returned file', content: new OA\MediaType(mediaType: 'text/csv')),
        ]
    )]
    public function export(CalculatorExportRequest $request)
    {
        $data = [['ID', 'Date', 'Amount', 'Portion', 'Monthly']];
        foreach ($this->prepareInstallmentGraph($request) as $item) {
            $data[] = [$item->id, $item->date, $item->amount, $item->portion, $item->monthly];
        }
        $collection = collect($data);
        $csv = $collection->reduce(function ($carry, $row) {
            return $carry . implode(',', $row) . PHP_EOL;
        }, '');
        $response = new Response($csv);

        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="calculator.csv"');

        return $response;
    }

    private function prepareInstallmentGraph(CalculatorRequest $request)
    {
        /** @var Tariff $tariff */
        $tariff = Tariff::query()->where('id', $request->tariff_id)->firstOrFail();
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
                $period->format('Y-m-d'),
                $amount / $request->period,
                $portion,
                $amount / $request->period + $portion
            );
            $i++;
        }

        return $calculated;
    }
}
