<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\AttachmentTypeEnum;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Installment\InstallmentCardAttachRequest;
use App\Http\Requests\Installment\InstallmentCardDetachRequest;
use App\Http\Requests\Installment\InstallmentCommentRequest;
use App\Http\Requests\Installment\InstallmentCommentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentCustomersListRequest;
use App\Http\Requests\Installment\InstallmentDocumentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentDocumentUploadRequest;
use App\Http\Requests\Installment\InstallmentGuarantorDetachRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentGuarantorUpdateRequest;
use App\Http\Requests\Installment\InstallmentViewRequest;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\Installment\InstallmentCardListResource;
use App\Http\Resources\Installment\InstallmentCommentResource;
use App\Http\Resources\Installment\InstallmentContactResource;
use App\Http\Resources\Installment\InstallmentCustomersListResource;
use App\Http\Resources\Installment\InstallmentGuarantorResource;
use App\Http\Resources\Installment\InstallmentTransactionResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Attachment;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentCard;
use App\Models\Company\InstallmentComment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Transaction;
use OpenApi\Attributes as OA;
use Throwable;

class InstallmentDetailsController extends Controller
{
    #[OA\Get(
        path: '/api/v1/installment/installment-details/{id}/installment-customers-card-list',
        description: 'Installment details route',
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'InstallmentCardListResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function installmentCustomersCardList(int $id, InstallmentViewRequest $request)
    {
        $model = Installment::query()
            ->with(['customer', 'guarantors', 'cards'])
            ->where('id', $id)
            ->firstOrFail();

        return new InstallmentCardListResource($model);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment-details/{id}/attach-card',
        description: 'Installment card attach card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardAttachRequest')
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function attachCard(int $id, InstallmentCardAttachRequest $request)
    {
        $installment = Installment::findOrFail($id);
        $installment->cards()->attach($request->card_id);

        return new StatusResource(true);
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment-details/{id}/detach-card',
        description: 'Installment detach Installment card route',
        requestBody: new OA\RequestBody(
            description: 'Installment card request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCardDetachRequest')
        ),
        tags: ['InstallmentDetails'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function detachCard(int $id, InstallmentCardDetachRequest $request): StatusResource
    {
        InstallmentCard::query()
            ->where('installment_id', $id)
            ->whereIn('card_id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment-details/{id}/guarantor-list',
        description: 'Installment guarantor list route',
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'InstallmentGuarantorResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function guarantorList(int $id, InstallmentViewRequest $request)
    {
        $installmentGuarantors = InstallmentGuarantor::query()
            ->where('installment_id', $id)
            ->with('customer')->get();

        return InstallmentGuarantorResource::collection($installmentGuarantors);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment-details/{id}/attach-guarantor',
        description: 'Installment attache Installment guarantor route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorRequest')
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentGuarantorResource'),
        ]
    )]
    public function attachGuarantor(int $id, InstallmentGuarantorRequest $request): InstallmentGuarantorResource
    {
        $attributes = $request->validated();
        $attributes['installment_id'] = $id;
        $attributes['any_card_charge'] = false;

        $model = InstallmentGuarantor::create($attributes);

        return new InstallmentGuarantorResource($model);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment-details/{id}/customers-list',
        description: 'Installment customers list route',
        tags: ['InstallmentDetails'],
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

    /**
     * @throws Throwable
     */
    #[OA\Put(
        path: '/api/v1/installment/installment-details/{id}/update-guarantor/{guarantor_id}',
        description: 'Installment guarantor update route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantor update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorUpdateRequest')
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
            new OA\Parameter(
                name: 'guarantor_id',
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
    public function updateGuarantor(int $id, int $guarantor_id, InstallmentGuarantorUpdateRequest $request): InstallmentGuarantorResource
    {
        $installmentGuarantor = InstallmentGuarantor::query()
            ->where('installment_id', $id)
            ->where('id', $guarantor_id)
            ->firstOrFail();

        $installmentGuarantor->updateOrFail($request->validated());

        return new InstallmentGuarantorResource($installmentGuarantor);
    }

    /**
     * @throws Throwable
     */
    #[OA\Delete(
        path: '/api/v1/installment/installment-details/{id}/detach-guarantor',
        description: 'Installment guarantors detach route',
        requestBody: new OA\RequestBody(
            description: 'Installment guarantors request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentGuarantorDetachRequest')
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function detachGuarantor(int $id, InstallmentGuarantorDetachRequest $request): StatusResource
    {
        InstallmentCard::query()
            ->whereHas('installment', fn ($query) => $query
                ->whereHas('guarantors', fn ($q) => $q->whereIn('installment_guarantors.id', $request->ids))
            )
            ->where('installment_id', $id)
            ->delete();
        //        delete guarantor
        InstallmentGuarantor::query()
            ->whereIn('id', $request->ids)
            ->where('installment_id', $id)
            ->delete();

        return new StatusResource(true);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment-details/{id}/comment-list',
        description: 'Installment guarantor list route',
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'InstallmentCommentListResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function commentList(int $id, InstallmentViewRequest $request)
    {
        $installmentComment = InstallmentComment::query()->where('installment_id', $id)->get();

        return InstallmentCommentResource::collection($installmentComment);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment-details/{id}/comment',
        description: 'Installment create Installment comment route',
        requestBody: new OA\RequestBody(
            description: 'Installment comment create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCommentRequest')
        ),
        tags: ['InstallmentDetails'],
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
    public function createComment(int $id, InstallmentCommentRequest $request): InstallmentCommentResource
    {
        $attributes = $request->validated();
        $attributes['installment_id'] = $id;
        $attributes['user_id'] = auth()->id();
        $model = InstallmentComment::create($attributes);

        return new InstallmentCommentResource($model);
    }

    /**
     * @throws Throwable
     */
    #[OA\Put(
        path: '/api/v1/installment/installment-details/{id}/comment/{comment_id}',
        description: 'Installment update Installment comment route',
        requestBody: new OA\RequestBody(
            description: 'Installment comment update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCommentRequest')
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OA\Parameter(
                name: 'comment_id',
                description: 'Installment Comment ID',
                in: 'path',
                required: true
            ),
            new OpenApiIdParameter(modelName: 'Installment'),

        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentCommentListResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function updateComment(int $id, int $comment_id, InstallmentCommentRequest $request): InstallmentCommentResource
    {
        $installmentComment = InstallmentComment::query()
            ->where('installment_id', $id)
            ->where('id', $comment_id)
            ->firstOrFail();
        $installmentComment->updateOrFail($request->validated());

        return new InstallmentCommentResource($installmentComment);
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment-details/{id}/comment',
        description: 'Installment Comment delete route',
        requestBody: new OA\RequestBody(
            description: 'Installment comments delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentCommentsDeleteRequest')
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function deleteComment(int $id, InstallmentCommentsDeleteRequest $request): StatusResource
    {
        InstallmentComment::query()
            ->where('installment_id', $id)
            ->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment-details/{id}/document-list',
        description: 'Installment guarantor list route',
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'AttachmentResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function documentList(int $id, InstallmentViewRequest $request)
    {
        $installment = Installment::query()->with('documents')->where('id', $id)->firstOrFail();

        return AttachmentResource::collection($installment->documents);
    }

    #[OA\Post(
        path: '/api/v1/installment/installment-details/{id}/document',
        description: 'Upload document route',
        requestBody: new OA\RequestBody(
            description: 'Installment Document upload body. Send as multipart/form-data',
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/InstallmentDocumentUploadRequest')
            ),
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'AttachmentResource'),
        ],
    )]
    public function uploadDocument(int $id, InstallmentDocumentUploadRequest $request): AttachmentResource
    {
        $installment = Installment::findOrFail($id);
        $document = Attachment::upload($request->file('file'), AttachmentTypeEnum::INSTALLMENT_DOCUMENT, $installment, $request->name);

        return new AttachmentResource($document);
    }

    #[OA\Delete(
        path: '/api/v1/installment/installment-details/{id}/document',
        description: 'Installment Documents delete route',
        requestBody: new OA\RequestBody(
            description: 'Installment documents delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentDocumentsDeleteRequest')
        ),
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function deleteDocument(int $id, InstallmentDocumentsDeleteRequest $request): StatusResource
    {
        Attachment::query()
            ->where('attachmentable_id', $id)
            ->where('attachmentable_type', Installment::class)
            ->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment-details/{id}/contact-list',
        description: 'Installment contact list route',
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Installment'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'InstallmentContactListResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function contactList(int $id, InstallmentViewRequest $request)
    {
        $installment = Installment::query()
            ->with(['customer', 'guarantors', 'customer.addresses', 'guarantors.addresses', 'customer.phones', 'guarantors.phones'])
            ->where('id', $id)
            ->firstOrFail();

        return new InstallmentContactResource($installment);
    }

    #[OA\Get(
        path: '/api/v1/installment/installment-details/{id}/transaction',
        description: 'Installment transaction list route',
        tags: ['InstallmentDetails'],
        parameters: [
            new OpenApiIdParameter(modelName: 'InstallmentId'),
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'InstallmentTransactionResource', withPagination: true, withSortables: true),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function transactionList(int $id, InstallmentViewRequest $request)
    {
        $installmentTransactions = Transaction::query()
            ->with(['customer'])
            ->where('installment_id', $id)
            ->paginate($request->per_page);

        return InstallmentTransactionResource::collection($installmentTransactions);
    }
}
