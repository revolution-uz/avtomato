<?php

namespace App\Http\Controllers\PartnerApi\V1\Customer;

use App\Clients\OilaKreditClient;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Customer\GetCustomerInfoByPassportRequest;
use App\Http\Resources\Customer\CustomerInfoByPassportDataResource;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class CustomerController extends Controller
{
    public function __construct(
        private readonly OilaKreditClient $oilaKreditClient
    )
    {
    }

    #[OA\Post(
        path: '/api/v1/customer/info-by-passport',
        description: 'Get info by passport',
        requestBody: new OA\RequestBody(
            description: 'Request to get customer info by passport data',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GetCustomerInfoByPassportRequest')
        ),
        tags: ['Customers'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CustomerInfoByPassportDataResource', nullable: true),
        ]
    )]
    public function infoByPassport(GetCustomerInfoByPassportRequest $request)
    {
        $oilaKreditInfo = $this->oilaKreditClient->getDataByPassport(
            passportSeries: $request->passport_series,
            passportNumber: $request->passport_number,
            birthDate: Carbon::parse($request->birth_date)
        );

        return ApiResponse::success($oilaKreditInfo ? new CustomerInfoByPassportDataResource($oilaKreditInfo) : null);
    }
}
