<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Exceptions\BalanceIsNotEnoughException;
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
use App\Http\Requests\Installment\InstallmentCardAttachRequest;
use App\Http\Requests\Installment\InstallmentCardDetachRequest;
use App\Http\Requests\Installment\InstallmentChangeStatusRequest;
use App\Http\Requests\Installment\InstallmentCommentRequest;
use App\Http\Requests\Installment\InstallmentCommentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentCustomersListRequest;
use App\Http\Requests\Installment\InstallmentDocumentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentDocumentUploadRequest;
use App\Http\Requests\Installment\InstallmentFilterRequest;
use App\Http\Requests\Installment\InstallmentGuarantorDetachRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentGuarantorUpdateRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentToggleRequest;
use App\Http\Requests\Installment\InstallmentUpdateRequest;
use App\Http\Requests\Installment\InstallmentViewRequest;
use App\Http\Resources\Installment\InstallmentCardResource;
use App\Http\Resources\Installment\InstallmentCommentListResource;
use App\Http\Resources\Installment\InstallmentCustomersListResource;
use App\Http\Resources\Installment\InstallmentGuarantorResource;
use App\Http\Resources\Installment\InstallmentResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Installment;
use App\Services\Installment\InstallmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Throwable;

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
                ->with(['customer', 'branch', 'tariff', 'graphs', 'cards', 'guarantors', 'documents', 'comments', 'transactions'])
                ->when($request->search, fn (Builder $q) => $q
                    ->whereHas('customer', fn (Builder $q) => $q
                        ->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name', 'ilike', "%{$request->search}%")
                        ->orWhere('middle_name', 'ilike', "%{$request->search}%")
                    )
                    ->orWhereHas('branch', fn (Builder $q) => $q->where('name', 'ilike', "%{$request->search}%"))
                    ->orWhereHas('tariff', fn (Builder $q) => $q->where('name', 'ilike', "%{$request->search}%"))
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
        path: '/api/v1/installment/installment/{id}/toggle-auto-charge',
        description: 'Installment toggle route',
        requestBody: new OA\RequestBody(
            description: 'Installment toggle request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentToggleRequest')
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
    public function toggleAutoCharge(int $id, InstallmentToggleRequest $request): InstallmentResource
    {
        return new InstallmentResource($this->installmentService->toggleAutoCharge(Installment::findOrFail($id), $request));
    }

    /**
     * @return InstallmentResource
     *
     * @throws BalanceIsNotEnoughException
     * @throws Throwable
     */
    #[OA\Post(
        path: '/api/v1/installment/installment/{id}/change-status',
        description: 'Installment change status route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentChangeStatusRequest'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function changeStatus(int $id, InstallmentChangeStatusRequest $request)
    {
        $installment = Installment::findOrFail($id);
        if ($this->installmentService->changeStatus($id, InstallmentStatusEnum::tryFrom($request->status))) {
            return new InstallmentResource($installment);
        }
        throw new AccessDeniedException('You cannot start this installment');
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
        $this->installmentService->deleteInstallments($request);

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment/{id}/attach-card',
        description: 'Installment card attach card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardAttachRequest')
        ),
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentCardResource'),
        ]
    )]
    public function attachCard(int $id, InstallmentCardAttachRequest $request): InstallmentCardResource
    {
        return new InstallmentCardResource($this->installmentService->attachCard($id, $request));
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment/detach-card',
        description: 'Installment detach Installment card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardDetachRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function detachCard(InstallmentCardDetachRequest $request): StatusResource
    {
        $this->installmentService->detachCards($request);

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment/{id}/attach-guarantor',
        description: 'Installment attache Installment guarantor route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorRequest')
        ),
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentGuarantorResource'),
        ]
    )]
    public function attachGuarantor(int $id, InstallmentGuarantorRequest $request): InstallmentGuarantorResource
    {
        return new InstallmentGuarantorResource($this->installmentService->attachGuarantor($id, $request));
    }

    #[OA\Get(
        path: '/api/v1/installment/installment/{id}/customers-list',
        description: 'Installment customers list route',
        tags: ['Installments'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'InstallmentCustomersListResource'),

        ]
    )]
    public function customersList(int $id, InstallmentCustomersListRequest $request): InstallmentCustomersListResource
    {
        $model = Installment::query()
            ->with(['customer', 'guarantors'])
            ->where('id', $id)
            ->firstOrFail();

        return new InstallmentCustomersListResource($model);
    }

    #[OA\Put(
        path: '/api/v1/installment/installment/update-guarantor/{id}',
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
    public function updateGuarantor(int $id, InstallmentGuarantorUpdateRequest $request): InstallmentGuarantorResource
    {
        return new InstallmentGuarantorResource($this->installmentService->updateGuarantor($id, $request));
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment/detach-guarantor',
        description: 'Installment guarantors detach route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantors request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorDetachRequest')
        ),
        tags: ['Installments'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function detachGuarantor(InstallmentGuarantorDetachRequest $request): StatusResource
    {
        $this->installmentService->detachGuarantors($request);

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment/{id}/comment',
        description: 'Installment create Installment comment route',
        requestBody: new OA\RequestBody(
            description: 'Installment comment create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCommentRequest')
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
            new OpenApiDataResponse(resourceName: 'InstallmentCommentListResource'),
        ]
    )]
    public function createComment(int $id, InstallmentCommentRequest $request): InstallmentCommentListResource
    {
        return new InstallmentCommentListResource($this->installmentService->createComment($id, $request));
    }

    #[OA\Put(
        path: '/api/v1/installment/installment/comment/{id}',
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
        return new InstallmentCommentListResource($this->installmentService->updateComment($id, $request));
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment/comment',
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
        $this->installmentService->deleteComment($request);

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment/{id}/document',
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
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentResource'),
        ],
    )]
    public function uploadDocument(int $id, InstallmentDocumentUploadRequest $request): InstallmentResource
    {
        return new InstallmentResource($this->installmentService->uploadAttachment(Installment::findOrFail($id), $request));
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment/document',
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
    public function deleteDocument(InstallmentDocumentsDeleteRequest $request): StatusResource
    {
        $this->installmentService->deleteAttachment($request->ids);

        return new StatusResource(true);
    }
}
