<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Installment\InstallmentCardRequest;
use App\Http\Requests\Installment\InstallmentFilterRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentViewRequest;
use App\Http\Resources\Installment\InstallmentCardResource;
use App\Http\Resources\Installment\InstallmentGuarantorResource;
use App\Http\Resources\Installment\InstallmentResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Card;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentCard;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Database\Eloquent\Builder;
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
            new OpenApiArrayResponse(resourceName: 'InstallmentResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(InstallmentFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'customer_id', 'tariff_id', 'amount']; // TODO add all fields, it's just for testing
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_VIEW);

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
                ->when($branches, fn (Builder $q) => $q->whereIn('branch_id', $branches))
                ->paginate($request->per_page)
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment/{id}',
        description: 'Installment get single route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, InstallmentViewRequest $request): InstallmentResource
    {
        return new InstallmentResource(Installment::findOrFail($id));
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
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
        ]
    )]
    public function create(InstallmentRequest $request): InstallmentResource
    {
        $model = new Installment;
        $model->fill($request->validated());
        $model->status = InstallmentStatusEnum::CREATED;
        $model->saveOrFail();

        return new InstallmentResource($model);
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
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, InstallmentRequest $request): InstallmentResource
    {
        $installment = Installment::findOrFail($id);
        $installment->fill($request->validated());
        $installment->saveOrFail();

        return new InstallmentResource($installment);
    }

    #[OA\Post(
        path: '/api/v1/installment/{installmentId}/attach-card',
        description: 'Installment card attach card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentCardResource'),
        ]
    )]
    public function attachCard(int $installmentId, InstallmentCardRequest $request): InstallmentCardResource
    {
        $model = new InstallmentCard;
        $model->fill($request->validated());
        $model->installment_id = $installmentId;
        $model->saveOrFail();

        return new InstallmentCardResource($model);
    }

    #[OA\Delete(
        path: '/api/v1/installment/{installmentId}/detach-card',
        description: 'Installment detach Installment card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function detachCard(int $installmentId, InstallmentCardRequest $request): StatusResource
    {
        $installmentCard = InstallmentCard::query()->where('installment_id', $installmentId)->where('card_id', $request->card_id)->first();

        return new StatusResource($installmentCard->delete());
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
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(InstallmentsDeleteRequest $request): StatusResource
    {
        Installment::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/{installmentId}/attach-guarantor',
        description: 'Installment attache Installment guarantor route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentGuarantorResource'),
        ]
    )]
    public function attachGuarantor(int $installmentId, InstallmentGuarantorRequest $request): InstallmentGuarantorResource
    {
        $model = new InstallmentGuarantor;
        $model->fill($request->validated());
        $model->installment_id = $installmentId;
        $model->saveOrFail();

        return new InstallmentGuarantorResource($model);
    }

    #[OA\Put(
        path: '/api/v1/installment/{installmentId}/update-guarantor',
        description: 'Installment guarantor update route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor update request body',
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
            new OpenApiDataResponse(resourceName: 'InstallmentGuarantorResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function updateGuarantor(int $installmentId, InstallmentGuarantorRequest $request): InstallmentGuarantorResource
    {
        $installmentGuarantor = InstallmentGuarantor::query()->where('installment_id', $installmentId)->where('customer_id', $request->customer_id)->first();
        $installmentGuarantor->fill($request->validated());

        $installmentGuarantor->saveOrFail();

        return new InstallmentGuarantorResource($installmentGuarantor);
    }

    #[OA\Delete(
        path: '/api/v1/installment/{installmentId}/detach-guarantor',
        description: 'Installment guarantor detach route',
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
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function detachGuarantor(int $installmentId, InstallmentGuarantorRequest $request): StatusResource
    {
        //        deleting guarantor all attached cards
        $cardIds = Card::query()->where('customer_id', $request->customer_id)->pluck('id')->toArray();
        InstallmentCard::query()->where('installment_id', $installmentId)->whereIn('card_id', $cardIds)->delete();
        //        delete guarantor
        InstallmentGuarantor::query()->where('installment_id', $installmentId)->where('customer_id', $request->customer_id)->delete();

        return new StatusResource(true);
    }
}
