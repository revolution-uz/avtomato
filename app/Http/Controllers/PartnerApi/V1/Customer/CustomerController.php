<?php

namespace App\Http\Controllers\PartnerApi\V1\Customer;

use App\Enums\Company\CardStatusEnum;
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
use App\Http\Requests\Customer\CardRequest;
use App\Http\Requests\Customer\CardsDeleteRequest;
use App\Http\Requests\Customer\ConfirmCustomerCardRequest;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\CustomersFilterRequest;
use App\Http\Requests\Customer\CustomerViewRequest;
use App\Http\Requests\Customer\DetachAddressRequest;
use App\Http\Requests\Customer\DetachPhoneRequest;
use App\Http\Requests\Customer\GetCustomerInfoByPassportRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\Customer\AttachCustomerCardResource;
use App\Http\Resources\Customer\CardResource;
use App\Http\Resources\Customer\CustomerListItemResource;
use App\Http\Resources\Customer\PassportInfo\CustomerInfoByPassportDataResource;
use App\Http\Resources\Customer\SingleCustomerResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use App\Services\PlumService;
use App\Services\Scoring\PassportInfoService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;
use Throwable;

class CustomerController extends Controller
{
    public function __construct(
        private readonly PassportInfoService $passportInfoService,
        private readonly PlumService $plumService
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
        $sortables = ['id', 'pinfl', 'passport_series', 'first_name', 'last_name'];

        return CustomerListItemResource::collection(
            Customer::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('first_name', 'ILIKE', '%' . $request->search . '%')
                        ->where('last_name', 'ILIKE', '%' . $request->search . '%')
                        ->where('middle_name', 'ILIKE', '%' . $request->search . '%')
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn (Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['sortables' => $sortables]);
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
            $customer = new Customer;
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

            return new SingleCustomerResource($customer);
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    #[OA\Put(
        path: '/api/v1/customer/{id}',
        description: 'Create a customer',
        requestBody: new OA\RequestBody(
            description: 'Request to create a customer',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCustomerRequest')
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
        $customer = Customer::findOrFail($id);
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

    #[OA\Post(
        path: '/api/v1/customer/{id}/card',
        description: 'Card attach route',
        requestBody: new OA\RequestBody(
            description: 'Card attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CardRequest')
        ),
        tags: ['Customers'],
        responses: [
            new OpenApiDataResponse(resourceName: 'AttachCustomerCardResource'),
        ]
    )]
    public function attachCard(int $id, CardRequest $request): AttachCustomerCardResource
    {
        $card = new Card;
        $card->fill($request->validated());
        $card->setPan();
        $card->status = CardStatusEnum::INACTIVE;
        $card->customer_id = $id;
        $card->saveOrFail();

        return AttachCustomerCardResource::make($this->plumService->attachCard($card), new CardResource($card));
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

    #[OA\Post(
        path: '/api/v1/customer/{id}/confirm-card',
        description: 'Card confirm route',
        requestBody: new OA\RequestBody(
            description: 'Card confirm request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ConfirmCustomerCardRequest')
        ),
        tags: ['Customers'],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardResource'),
        ]
    )]
    public function confirmCard(int $id, ConfirmCustomerCardRequest $request): CardResource
    {
        $card = Card::query()
            ->where('customer_id', $id)
            ->findOrFail($request->card_id);
        $this->plumService->confirmCard($card, $request->session, $request->otp);

        return CardResource::make($card);
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
        responses: [
            new OpenApiDataResponse(resourceName: 'AttachCustomerCardResource'),
        ]
    )]
    public function attachPhone(int $id, AttachPhoneRequest $request): SingleCustomerResource
    {
        $customer = Customer::findOrFail($id);
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
            content: new OA\JsonContent(ref: '#/components/schemas/DetachPhoneRequest')
        ),
        tags: ['Customers'],
        responses: [
            new OpenApiDataResponse('StatusResource'),
        ]
    )]
    public function detachPhone(int $id, DetachPhoneRequest $request): StatusResource
    {
        $customer = Customer::findOrFail($id);
        $customer->phones()->detach($request->phone);

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
            content: new OA\JsonContent(ref: '#/components/schemas/DetachPhoneRequest')
        ),
        tags: ['Customers'],
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
