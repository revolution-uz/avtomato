<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use _PHPStan_c6b09fbdf\Symfony\Component\Finder\Exception\AccessDeniedException;
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
use App\Http\Requests\Installment\InstallmentCancelRequest;
use App\Http\Requests\Installment\InstallmentCardRequest;
use App\Http\Requests\Installment\InstallmentCommentRequest;
use App\Http\Requests\Installment\InstallmentCommentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentDocumentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentDocumentUploadRequest;
use App\Http\Requests\Installment\InstallmentFilterRequest;
use App\Http\Requests\Installment\InstallmentFinishRequest;
use App\Http\Requests\Installment\InstallmentFreezeRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentGuarantorUpdateRequest;
use App\Http\Requests\Installment\InstallmentImagesDeleteRequest;
use App\Http\Requests\Installment\InstallmentImageUploadRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentStartRequest;
use App\Http\Requests\Installment\InstallmentUpdateRequest;
use App\Http\Requests\Installment\InstallmentViewRequest;
use App\Http\Resources\Installment\InstallmentCardResource;
use App\Http\Resources\Installment\InstallmentCommentListResource;
use App\Http\Resources\Installment\InstallmentGuarantorResource;
use App\Http\Resources\Installment\InstallmentResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Card;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentCard;
use App\Models\Company\InstallmentComment;
use App\Models\Company\InstallmentGuarantor;
use App\Services\Installment\InstallmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class InstallmentController extends Controller
{
    public function __construct(
        protected InstallmentService $installmentService
    ) {
    }

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
        path: '/api/v1/installment/{id}/start',
        description: 'Installment start route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function start(int $id, InstallmentStartRequest $request)
    {
        $installment = Installment::findOrFail($id);
        if ($installment->start()) {
            return new InstallmentResource($installment);
        }
        throw new AccessDeniedException('You cannot start this installment');
    }

    #[OA\Post(
        path: '/api/v1/installment/{id}/cancel',
        description: 'Installment cancel route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function cancel(int $id, InstallmentCancelRequest $request)
    {
        $installment = Installment::findOrFail($id);
        if ($installment->cancel()) {
            return new InstallmentResource($installment);
        }
        throw new AccessDeniedException('You cannot cancel this installment');
    }

    #[OA\Post(
        path: '/api/v1/installment/{id}/freeze',
        description: 'Installment freeze route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function freeze(int $id, InstallmentFreezeRequest $request)
    {
        $installment = Installment::findOrFail($id);
        if ($installment->freeze()) {
            return new InstallmentResource($installment);
        }
        throw new AccessDeniedException('You cannot cancel this installment');
    }

    #[OA\Post(
        path: '/api/v1/installment/{id}/finish',
        description: 'Installment finish route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function finish(int $id, InstallmentFinishRequest $request)
    {
        $installment = Installment::findOrFail($id);
        if ($installment->finish()) {
            return new InstallmentResource($installment);
        }
        throw new AccessDeniedException('You cannot cancel this installment');
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
        return new InstallmentResource($this->installmentService->create($request));
    }

    #[OA\Put(
        path: '/api/v1/installment/installment/{id}',
        description: 'Installment update route',
        requestBody: new OA\RequestBody(
            description: 'Installment update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentUpdateRequest')
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
    public function update(int $id, InstallmentUpdateRequest $request): InstallmentResource
    {
        return new InstallmentResource($this->installmentService->updateDetails(Installment::findOrFail($id), $request));
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
        Installment::query()->whereIn('status', InstallmentStatusEnum::getStatusListByStatus())->whereIn('id', $request->ids)->delete();

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
        $model->any_card_charge = false;
        $model->saveOrFail();

        return new InstallmentGuarantorResource($model);
    }

    #[OA\Put(
        path: '/api/v1/installment/{installmentId}/update-guarantor',
        description: 'Installment guarantor update route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorUpdateRequest')
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
    public function updateGuarantor(int $installmentId, InstallmentGuarantorUpdateRequest $request): InstallmentGuarantorResource
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

    #[OA\Post(
        path: '/api/v1/installment/comment',
        description: 'Installment create Installment comment route',
        requestBody: new OA\RequestBody(
            description: 'Installment comment create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCommentRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentCommentListResource'),
        ]
    )]
    public function createComment(InstallmentCommentRequest $request): InstallmentCommentListResource
    {
        $installmentComment = new InstallmentComment;
        $installmentComment->fill($request->validated());
        $installmentComment->user_id = auth()->id();
        $installmentComment->saveOrFail();

        return new InstallmentCommentListResource($installmentComment);
    }

    #[OA\Put(
        path: '/api/v1/installment/comment/{id}',
        description: 'Installment update Installment comment route',
        requestBody: new OA\RequestBody(
            description: 'Installment comment update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCommentRequest')
        ),
        tags: ['Installments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Installment Comment ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentCommentListResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function updateComment(int $id, InstallmentCommentRequest $request): InstallmentCommentListResource
    {
        $installmentComment = InstallmentComment::findOrFail($id);
        $installmentComment->fill($request->validated());
        $installmentComment->saveOrFail();

        return new InstallmentCommentListResource($installmentComment);
    }

    #[OA\Delete(
        path: '/api/v1/installment/comment',
        description: 'Installment Comment delete route',
        requestBody: new OA\RequestBody(
            description: 'Installment comments delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCommentsDeleteRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function deleteComment(InstallmentCommentsDeleteRequest $request): StatusResource
    {
        InstallmentComment::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/{id}/document',
        description: 'Upload document route',
        requestBody: new OA\RequestBody(
            description: 'Installment Document upload body. Send as multipart/form-data',
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/InstallmentDocumentUploadRequest')
            ),
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
        ],
    )]
    public function uploadDocument(int $id, InstallmentDocumentUploadRequest $request): InstallmentResource
    {
        return new InstallmentResource($this->installmentService->uploadAttachment(Installment::findOrFail($id), $request));
    }

    #[OA\Delete(
        path: '/api/v1/installment/document',
        description: 'Installment Documents delete route',
        requestBody: new OA\RequestBody(
            description: 'Installment documents delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentDocumentsDeleteRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function deleteDocuments(InstallmentDocumentsDeleteRequest $request): StatusResource
    {
        $this->installmentService->deleteAttachment($request->ids);

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/{id}/image',
        description: 'Upload image route',
        requestBody: new OA\RequestBody(
            description: 'Installment Image upload body. Send as multipart/form-data',
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/InstallmentImageUploadRequest')
            ),
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
        ],
    )]
    public function uploadImage(int $id, InstallmentImageUploadRequest $request): InstallmentResource
    {
        return new InstallmentResource($this->installmentService->uploadImage(Installment::findOrFail($id), $request));
    }

    #[OA\Delete(
        path: '/api/v1/installment/image',
        description: 'Installment Images delete route',
        requestBody: new OA\RequestBody(
            description: 'Installment images delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentImagesDeleteRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function deleteImages(InstallmentImagesDeleteRequest $request): StatusResource
    {
        $this->installmentService->deleteAttachment($request->ids);

        return new StatusResource(true);
    }
}
