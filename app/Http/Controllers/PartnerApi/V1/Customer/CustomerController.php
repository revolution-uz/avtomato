<?php

namespace App\Http\Controllers\PartnerApi\V1\Customer;

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
use App\Http\Requests\Customer\AttachAddressRequest;
use App\Http\Requests\Customer\AttachPhoneRequest;
use App\Http\Requests\Customer\CardRemoveRequest;
use App\Http\Requests\Customer\CardRequest;
use App\Http\Requests\Customer\CardsDeleteRequest;
use App\Http\Requests\Customer\ConfirmCustomerCardRequest;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\CustomerByPassportRequest;
use App\Http\Requests\Customer\CustomersFilterRequest;
use App\Http\Requests\Customer\CustomerViewInstallmentRequest;
use App\Http\Requests\Customer\CustomerViewRequest;
use App\Http\Requests\Customer\DetachAddressRequest;
use App\Http\Requests\Customer\DetachPhonesRequest;
use App\Http\Requests\Customer\GetCustomerInfoByPassportRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\Customer\AttachCustomerCardResource;
use App\Http\Resources\Customer\CardRemoveResource;
use App\Http\Resources\Customer\CardResource;
use App\Http\Resources\Customer\CustomerListItemResource;
use App\Http\Resources\Customer\ErrorConfirmCardResource;
use App\Http\Resources\Customer\PassportInfo\CustomerInfoByPassportDataResource;
use App\Http\Resources\Customer\SingleCustomerResource;
use App\Http\Resources\Installment\InstallmentResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Phone;
use App\Services\AtmosService;
use App\Services\CustomerCardService;
use App\Services\CustomerService;
use App\Services\Scoring\PassportInfoService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;
use Throwable;

class CustomerController extends Controller
{
    public function __construct(
        private readonly PassportInfoService $passportInfoService,
        private readonly AtmosService $atmosCardService,
        private readonly CustomerService $customerService,
        private readonly CustomerCardService $customerCardService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/customer',
        description: 'Get customers list route',
        tags: ['Customers'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'CustomerListItemResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function index(CustomersFilterRequest $request): AnonymousResourceCollection
    {
        $request->sortables = ['id', 'pinfl', 'passport_series', 'passport_number', 'first_name', 'last_name', 'birth_date'];

        return CustomerListItemResource::collection(
            Customer::query()
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('first_name', 'ILIKE', $search)
                            ->orWhere('last_name', 'ILIKE', $search)
                            ->orWhere('middle_name', 'ILIKE', $search)
                            ->orWhere('passport_series', 'ILIKE', $search)
                            ->orWhere('passport_number', 'ILIKE', $search);
                    });
                })
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
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
            new OpenApiDataResponse(resourceName: 'CustomerInfoByPassportDataResource', nullable: true),
        ]
    )]
    public function infoByPassport(GetCustomerInfoByPassportRequest $request)
    {
        $passportData = $this->passportInfoService->getPassportData(
            passportSeries: $request->passport_series,
            passportNumber: $request->passport_number,
            birthData: Carbon::parse($request->birth_date)
        );

        return new CustomerInfoByPassportDataResource($passportData);
    }

    #[OA\Post(
        path: '/api/v1/customer/by-passport',
        description: 'Get single customer by passport info',
        requestBody: new OA\RequestBody(
            description: 'Request to find a customer by passport',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CustomerByPassportRequest')
        ),
        tags: ['Customers'],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleCustomerResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function findByPassport(CustomerByPassportRequest $request): SingleCustomerResource
    {
        return new SingleCustomerResource(
            Customer::query()
                ->when($request->pinfl, fn (Builder $q) => $q->where('pinfl', $request->pinfl))
                ->when(
                    $request->passport_series,
                    fn (Builder $q) => $q
                        ->where('passport_series', $request->passport_series)
                        ->where('passport_number', $request->passport_number)
                )->firstOrFail()
        );
    }

    /**
     * @return SingleCustomerResource
     *
     * @throws Throwable
     */
    #[OA\Post(
        path: '/api/v1/customer',
        description: 'Create a customer',
        requestBody: new OA\RequestBody(
            description: 'Request to create a customer',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCustomerRequest')
        ),
        tags: ['Customers'],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleCustomerResource', response: '201'),
        ]
    )]
    public function create(CreateCustomerRequest $request)
    {
        DB::beginTransaction();

        try {
            $customer = $this->customerService->saveCustomer($request);

            foreach ($request->phones as $phoneInput) {
                /** @var Phone $phone */
                $phone = Phone::query()->firstOrCreate(['phone' => $phoneInput['phone']]);

                $customer->phones()->attach($phone->id, ['type' => $phoneInput['type'], 'comment' => $phoneInput['comment']]);
            }

            $customer->addresses()->createMany($request->addresses);

            DB::commit();

            return new SingleCustomerResource($customer);
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    /**
     * @return SingleCustomerResource
     *
     * @throws Throwable
     */
    #[OA\Put(
        path: '/api/v1/customer/{id}',
        description: 'Update a customer',
        requestBody: new OA\RequestBody(
            description: 'Request to update a customer',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateCustomerRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleCustomerResource', response: '201'),
        ]
    )]
    public function update(int $id, UpdateCustomerRequest $request)
    {
        $customer = $this->customerService->saveCustomer($request, $id);

        return new SingleCustomerResource($customer);
    }

    #[OA\Get(
        path: '/api/v1/customer/{id}',
        description: 'Get single customer record route',
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleCustomerResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, CustomerViewRequest $request): SingleCustomerResource
    {
        return new SingleCustomerResource(Customer::findOrFail($id));
    }

    #[OA\Get(
        path: '/api/v1/customer/{id}/installments',
        description: 'Get customer installments route',
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer ID'),
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'InstallmentResource', withPagination: true, withSortables: true),
        ]
    )]
    public function installments(int $id, CustomerViewInstallmentRequest $request): AnonymousResourceCollection
    {

        $request->sortables = ['id', 'tariff_id', 'amount', 'created_at', 'starts_in', 'next_pay_date']; // TODO add all fields, it's just for testing
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::INSTALLMENT_VIEW);

        return InstallmentResource::collection(
            Installment::query()
                ->with(['customer', 'branch', 'tariff', 'graphs', 'cards', 'guarantors', 'documents', 'comments', 'transactions'])
                ->where('customer_id', $id)
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->when($branches, fn (Builder $q) => $q->whereIn('branch_id', $branches))
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
    }

    /**
     * @throws Throwable
     */
    #[OA\Post(
        path: '/api/v1/customer/{id}/card',
        description: 'Card attach route',
        requestBody: new OA\RequestBody(
            description: 'Card attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CardRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'AttachCustomerCardResource'),
        ]
    )]
    public function attachCard(int $id, CardRequest $request): AttachCustomerCardResource
    {
        [$card, $cardServiceResponse] = $this->customerCardService->attachCard($id, $request);

        return AttachCustomerCardResource::make($card, $cardServiceResponse);
    }

    /**
     * @throws Throwable
     */
    #[OA\Post(
        path: '/api/v1/customer/{id}/confirm-card',
        description: 'Card confirm route',
        requestBody: new OA\RequestBody(
            description: 'Card confirm request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ConfirmCustomerCardRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardResource'),
            new OpenApiDataResponse(resourceName: 'ErrorConfirmCardResource', response: 401),
        ]
    )]
    public function confirmCard(int $id, ConfirmCustomerCardRequest $request): CardResource|ErrorConfirmCardResource
    {
        [$card, $cardServiceResponse] = $this->customerCardService->confirmCard($id, $request);

        if ($cardServiceResponse->result->isSucces()) {
            return CardResource::make($card);
        }

        return ErrorConfirmCardResource::make($cardServiceResponse->result, CardResource::make($card));
    }

    #[OA\Delete(
        path: '/api/v1/customer/{id}/card',
        description: 'Card delete route',
        requestBody: new OA\RequestBody(
            description: 'Card delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CardsDeleteRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse('StatusResource'),
        ]
    )]
    public function detachCard(int $id, CardsDeleteRequest $request): StatusResource
    {
        Card::query()
            ->whereIn('id', $request->ids)
            ->where('customer_id', $id)
            ->delete();

        return new StatusResource(true);
    }

    #[OA\Delete(
        path: '/api/v1/customer/{id}/remove-card',
        description: 'Card remove route',
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Card Id'),
        ],
        responses: [
            new OpenApiDataResponse('CardRemoveResource'),
        ]
    )]
    public function removeCard(int $id, CardRemoveRequest $request): CardRemoveResource
    {
        return $this->atmosCardService->removeCard(Card::findOrFail($id));
    }

    #[OA\Post(
        path: '/api/v1/customer/{id}/phone',
        description: 'Phone attach route',
        requestBody: new OA\RequestBody(
            description: 'Phone attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AttachPhoneRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'AttachCustomerCardResource'),
        ]
    )]
    public function attachPhone(int $id, AttachPhoneRequest $request): SingleCustomerResource
    {
        $customer = Customer::findOrFail($id);
        /** @var Phone $phone */
        $phone = Phone::query()->firstOrCreate(['phone' => $request->phone]);

        $customer->phones()->attach($phone->id, ['type' => $request->type, 'comment' => $request->comment]);

        return SingleCustomerResource::make(Customer::findOrFail($id));
    }

    #[OA\Delete(
        path: '/api/v1/customer/{id}/phone',
        description: 'Phone delete route',
        requestBody: new OA\RequestBody(
            description: 'Phone delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DetachPhonesRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse('StatusResource'),
        ]
    )]
    public function detachPhone(int $id, DetachPhonesRequest $request): StatusResource
    {
        $customer = Customer::findOrFail($id);
        foreach ($request->phones as $phone) {
            /** @var Phone $phone */
            $phone = Phone::query()->where('phone', $phone)->firstOrFail();
            $customer->phones()->detach($phone->id);
        }

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/customer/{id}/address',
        description: 'Address attach route',
        requestBody: new OA\RequestBody(
            description: 'Phone attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AttachAddressRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'AttachCustomerCardResource'),
        ]
    )]
    public function attachAddress(int $id, AttachAddressRequest $request): SingleCustomerResource
    {
        $customer = Customer::findOrFail($id);
        $customer->addresses()->create($request->validated());

        return SingleCustomerResource::make(Customer::findOrFail($id));
    }

    #[OA\Delete(
        path: '/api/v1/customer/{id}/address',
        description: 'Address delete route',
        requestBody: new OA\RequestBody(
            description: 'Address delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DetachAddressRequest')
        ),
        tags: ['Customers'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Customer'),
        ],
        responses: [
            new OpenApiDataResponse('StatusResource'),
        ]
    )]
    public function detachAddress(int $id, DetachAddressRequest $request): StatusResource
    {
        $customer = Customer::findOrFail($id);
        $customer->addresses()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
