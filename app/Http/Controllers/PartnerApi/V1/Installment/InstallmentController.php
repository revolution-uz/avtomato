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
use App\Http\Requests\Installment\InstallmentChangeStatusRequest;
use App\Http\Requests\Installment\InstallmentFilterRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentToggleRequest;
use App\Http\Requests\Installment\InstallmentUpdateRequest;
use App\Http\Requests\Installment\InstallmentViewRequest;
use App\Http\Resources\Installment\InstallmentGuarantorResource;
use App\Http\Resources\Installment\InstallmentResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Services\Installment\InstallmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Throwable;

class InstallmentController extends Controller
{
    public function __construct(
        protected InstallmentService $installmentService
    ) {}

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
        $request->sortables = ['id', 'customer_id', 'tariff_id', 'amount', 'created_at', 'starts_in', 'next_pay_date']; // TODO add all fields, it's just for testing
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
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->when($branches, fn (Builder $q) => $q->whereIn('branch_id', $branches))
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
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
    public function single(int $id, InstallmentViewRequest $request): InstallmentResource
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
        return new InstallmentResource($this->installmentService->create($request->validated()));
    }

    /**
     * @throws Throwable
     */
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
        $installment = Installment::findOrFail($id);

        if ($installment->status == InstallmentStatusEnum::IN_ACTIVE) {
            $installment->updateOrFail($request->validated());
        }

        return new InstallmentResource($installment);
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
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
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    #[OA\Post(
        path: '/api/v1/installment/installment/{id}/toggle-guarantor-auto-charge',
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
    public function toggleGuarantorAutoCharge(int $id, InstallmentToggleRequest $request): InstallmentGuarantorResource
    {
        return new InstallmentGuarantorResource($this->installmentService->toggleGuarantorAutoCharge(InstallmentGuarantor::findOrFail($id), $request));
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/InstallmentChangeStatusRequest'),
        ),
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
        if ($this->installmentService->changeStatus($installment, InstallmentStatusEnum::tryFrom($request->status))) {
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
        $count = Installment::query()->whereIn('status', InstallmentStatusEnum::getStatusListByStatus())->whereIn('id', $request->ids)->count();

        if ($count !== count($request->ids)) {
            throw new AccessDeniedException('You cannot delete this installment');
        }

        Installment::query()->whereIn('status', InstallmentStatusEnum::getStatusListByStatus())->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
