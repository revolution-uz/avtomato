<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Installment\InstallmentCardRequest;
use App\Http\Requests\Installment\InstallmentFilterRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Resources\Installment\InstallmentCardResource;
use App\Http\Resources\Installment\InstallmentGuarantorResource;
use App\Http\Resources\Installment\InstallmentResource;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentCard;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class InstallmentController extends Controller
{
    #[OA\Get(
        path: '/api/v1/installment/installment',
        description: 'Installment route',
        tags: ['Installments'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'InstallmentResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(InstallmentFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'customer_id', 'tariff_id', 'amount']; // TODO add all fields, it's just for testing

        return InstallmentResource::collection(
            Installment::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q->where('id', $request->search)
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn (Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['success' => true, 'sortables' => $sortables]);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment/{id}',
        description: 'Installment get single route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(Installment $installment): JsonResponse
    {
        return ApiResponse::success(InstallmentResource::make($installment));
    }

    #[OA\Post(
        path: '/api/v1/installment/installment',
        description: 'Installment create route',
        requestBody: new OA\RequestBody(
            description: 'Installment create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'InstallmentResource'),
        ]
    )]
    public function create(InstallmentRequest $request): JsonResponse
    {
        $model = new Installment;
        $model->fill($request->validated());

        if ($model->save()) {
            return ApiResponse::success(InstallmentResource::make($model));
        }

        return ApiResponse::error('Error on creating Installment');
    }

    #[OA\Put(
        path: '/api/v1/installment/installment/{id}',
        description: 'Installment update route',
        requestBody: new OA\RequestBody(
            description: 'Installment update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentRequest')
        ),
        tags: ['Installments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Installment ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(Installment $installment, InstallmentRequest $request): JsonResponse
    {
        $installment->fill($request->validated());

        if ($installment->save()) {
            return ApiResponse::success(InstallmentResource::make($installment));
        }

        return ApiResponse::error('Error on updating Installment');
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment',
        description: 'Installment delete route',
        requestBody: new OA\RequestBody(
            description: 'Installments delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentsDeleteRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(InstallmentsDeleteRequest $request): JsonResponse
    {
        Installment::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment/card',
        description: 'Installment card attach card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'InstallmentCardResource'),
        ]
    )]
    public function attachCard(InstallmentCardRequest $request): JsonResponse
    {
        $model = new InstallmentCard;
        $model->fill($request->validated());

        if ($model->save()) {
            return ApiResponse::success(InstallmentCardResource::make($model));
        }

        return ApiResponse::error('Error on attaching card to Installment');
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment/card',
        description: 'Installment detach Installment card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function detachCard(InstallmentCardRequest $request): JsonResponse
    {
        $installmentCard = InstallmentCard::query()->where('installment_id', $request->installment_id)->where('card_id', $request->card_id)->first();

        return ApiResponse::success($installmentCard->delete());
    }

    #[OA\Post(
        path: '/api/v1/installment/installment/guarantor',
        description: 'Installment attache Installment guarantor route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'InstallmentGuarantorResource'),
        ]
    )]
    public function attachGuarantor(InstallmentGuarantorRequest $request): JsonResponse
    {
        $model = new InstallmentGuarantor;
        $model->fill($request->validated());

        if ($model->save()) {
            return ApiResponse::success(InstallmentGuarantorResource::make($model));
        }

        return ApiResponse::error('Error on attaching guarantor to Installment');
    }

    #[OA\Put(
        path: '/api/v1/installment/installment/guarantor',
        description: 'Installment guarantor create route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorRequest')
        ),
        tags: ['Installments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Installment Guarantor ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'InstallmentGuarantorResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function updateGuarantor(InstallmentGuarantor $installmentGuarantor, InstallmentGuarantorRequest $request): JsonResponse
    {
        $installmentGuarantor->fill($request->validated());

        if ($installmentGuarantor->save()) {
            return ApiResponse::success(InstallmentGuarantorResource::make($installmentGuarantor));
        }

        return ApiResponse::error('Error on updating Installment');
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment/guarantor',
        description: 'Installment detach Installment card route',
        tags: ['Installments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Installment Guarantor ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function detachGuarantor(InstallmentGuarantor $installmentGuarantor): JsonResponse
    {
        //before detaching Guarantor delete all his card from installment
        return ApiResponse::success($installmentGuarantor->delete());
    }
}
