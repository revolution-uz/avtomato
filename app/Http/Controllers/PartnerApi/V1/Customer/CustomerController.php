<?php

namespace App\Http\Controllers\PartnerApi\V1\Customer;

use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\GetCustomerInfoByPassportRequest;
use App\Http\Resources\Customer\CustomerInfoByPassportDataResource;
use App\Http\Resources\Customer\SingleCustomerResource;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use App\Services\Scoring\PassportInfoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;
use Throwable;

class CustomerController extends Controller
{
    public function __construct(
        private readonly PassportInfoService $passportInfoService
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
        $passportData = $this->passportInfoService->getPassportData(
            passportSeries: $request->passport_series,
            passportNumber: $request->passport_number,
            birthData: Carbon::parse($request->birth_date)
        );

        return ApiResponse::success($passportData ? new CustomerInfoByPassportDataResource($passportData) : null);
    }

    #[OA\Post(
        path: '/api/v1/customer/create',
        description: 'Create a customer',
        requestBody: new OA\RequestBody(
            description: 'Request to create a customer',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCustomerRequest')
        ),
        tags: ['Customers'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleCustomerResource'),
        ]
    )]
    public function create(CreateCustomerRequest $request)
    {
        DB::beginTransaction();

        try {
            $customer = new Customer();
            $customer->pinfl = $request->pinfl;
            $customer->tin = $request->tin;
            $customer->passport_series = $request->passport_series;
            $customer->passport_number = $request->passport_number;
            $customer->passport_given_date = Carbon::parse($request->passport_given_date);
            $customer->passport_expiration_date = Carbon::parse($request->passport_expiration_date);
            $customer->passport_given_place = $request->passport_given_place;
            $customer->passport_citizenship = $request->passport_citizenship;
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->middle_name = $request->middle_name;
            $customer->birth_date = Carbon::parse($request->birth_date);
            $customer->birth_place = $request->birth_place;
            $customer->gender = $request->gender;

            $customer->saveOrFail();

            foreach ($request->phones as $phoneInput) {
                /** @var Phone $phone */
                $phone = Phone::query()->firstOrCreate(['phone' => $phoneInput['phone']]);

                $customer->phones()->attach($phone->id, ['type' => $phoneInput['type'], 'comment' => $phoneInput['comment']]);
            }

            $customer->addresses()->createMany($request->addresses);

            DB::commit();

            return ApiResponse::success(new SingleCustomerResource($customer));
        } catch (Throwable $th) {
            DB::rollBack();

            return ApiResponse::error($th->getMessage());
        }
    }

    #[OA\Get(
        path: '/api/v1/customer/{id}',
        description: 'Get single customer record route',
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer')
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleCustomerResource'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function getSingle(Customer $customer): JsonResponse
    {
        return ApiResponse::success(SingleCustomerResource::make($customer));
    }
}
