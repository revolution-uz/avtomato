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
use OpenApi\Attributes as OA;

class InstallmentController extends Controller
{

    #[OA\Post(
        path: '/api/v1/installment/calculator',
        operationId: 'installmentCalculator',
        description: 'Installment calculator route',
        summary: 'Installment calculator route',
        requestBody: new OA\RequestBody(
            description: 'Calculator request body',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'tariff_id', type: 'number', example: 1),
                    new OA\Property(property: 'amount', type: 'number', example: 1000),
                    new OA\Property(property: 'initial_amount', type: 'number', example: 0),
                    new OA\Property(property: 'period', type: 'number', example: 12),
                ]
            )
        ),
        tags: ['Installment'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => [
                            [
                                'id' => 1,
                                'date' => '2023-09-22',
                                'amount' => 10000,
                                'portion' => 14400,
                                'monthly' => 24400,
                            ],
                            [
                                'id' => 2,
                                'date' => '2023-10-22',
                                'amount' => 10000,
                                'portion' => 14400,
                                'monthly' => 24400,
                            ],
                            [
                                'id' => 3,
                                'date' => '2023-11-22',
                                'amount' => 10000,
                                'portion' => 14400,
                                'monthly' => 24400,
                            ],
                        ],
                    ]
                )
            ),
        ]
    )]
    public function calculator(CalculatorRequest $request): JsonResponse
    {
        return ApiResponse::success($this->prepareIntallmentGraph($request));
    }

    #[OA\Post(
        path: '/api/v1/installment/export-calculator/csv',
        operationId: 'ExportInstallmentCalculator',
        description: 'Export installment calculator route',
        summary: 'Export installment calculator route',
        requestBody: new OA\RequestBody(
            description: 'Calculator request body',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'tariff_id', type: 'number', example: 1),
                    new OA\Property(property: 'amount', type: 'number', example: 1000),
                    new OA\Property(property: 'initial_amount', type: 'number', example: 0),
                    new OA\Property(property: 'period', type: 'number', example: 12),
                ]
            )
        ),
        tags: ['Installment'],
        responses: [
            new OA\Response(response: 200, description: 'Returned file', content: new OA\MediaType(mediaType: 'text/csv')),
        ]
    )]
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
