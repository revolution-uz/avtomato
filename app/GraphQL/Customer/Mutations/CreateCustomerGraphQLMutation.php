<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\AddressTypeEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\GenderEnum;
use App\GraphQL\Common\Types\AddressGraphQLType;
use App\GraphQL\Common\Types\PhoneGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\Helpers\RbacHelper;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Resources\Customer\SingleCustomerResource;
use App\Models\Company\Phone;
use App\Services\CustomerService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateCustomerGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCustomer',
    ];

    public function __construct(
        private readonly CustomerService $customerService,
    ) {

    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::nonNull(Type::int()),
            ],
            'tin' => [
                'name' => 'tin',
                'type' => Type::int(),
            ],
            'passport_series' => [
                'name' => 'passport_series',
                'type' => Type::nonNull(Type::string()),
            ],
            'passport_number' => [
                'name' => 'passport_number',
                'type' => Type::nonNull(Type::string()),
            ],
            'passport_given_date' => [
                'name' => 'passport_given_date',
                'type' => Type::nonNull(Type::string()),
            ],
            'passport_expiration_date' => [
                'name' => 'passport_expiration_date',
                'type' => Type::nonNull(Type::string()),
            ],
            'passport_given_place' => [
                'name' => 'passport_given_place',
                'type' => Type::string(),
            ],
            'passport_citizenship' => [
                'name' => 'passport_given_place',
                'type' => Type::string(),
            ],
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::string(),
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::string(),
            ],
            'middle_name' => [
                'name' => 'middle_name',
                'type' => Type::string(),
            ],
            'birth_date' => [
                'name' => 'birth_date',
                'type' => Type::nonNull(Type::string()),
            ],
            'birth_place' => [
                'name' => 'birth_place',
                'type' => Type::string(),
            ],
            'gender' => [
                'name' => 'gender',
                'type' => Type::nonNull(Type::int()),
            ],
            'phones' => [
                'name' => 'phones',
                'type' => GraphQL::type(PhoneGraphQLType::GRAPHQL_TYPE_NAME),
            ],
            'addresses' => [
                'name' => 'addresses',
                'type' => GraphQL::type(AddressGraphQLType::GRAPHQL_TYPE_NAME),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        DB::beginTransaction();

        try {
            $request = new CreateCustomerRequest($args);
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

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'pinfl' => [
                'required',
                'integer',
                'digits:14',
                'regex:/^[0-9]+$/',
                Rule::unique('customers', 'pinfl'),
            ],
            'tin' => [
                'nullable',
                'integer',
                'digits:9',
                'regex:/^[0-9]+$/',
                Rule::unique('customers', 'tin'),
            ],
            'passport_series' => [
                'required',
                'string',
                'size:2',
                'regex:/^[A-Z]+$/',
            ],
            'passport_number' => [
                'required',
                'string',
                'size:7',
                'regex:/^[0-9]+$/',
                Rule::unique('customers', 'passport_number')
                    ->where('passport_series', $this->passport_series),
            ],
            'passport_given_date' => [
                'required',
                'date',
            ],
            'passport_expiration_date' => [
                'required',
                'date',
            ],
            'passport_given_place' => [
                'nullable',
                'string',
                'max:128',
            ],
            'passport_citizenship' => [
                'nullable',
                'string',
                'max:32',
            ],
            'first_name' => [
                'required',
                'string',
                'max:32',
            ],
            'last_name' => [
                'required',
                'string',
                'max:32',
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:32',
            ],
            'birth_date' => [
                'required',
                'date',
            ],
            'birth_place' => [
                'nullable',
                'string',
                'max:32',
            ],
            'gender' => [
                'required',
                'integer',
                Rule::in(GenderEnum::values()),
            ],
            'phones' => [
                'nullable',
                'array',
            ],
            'phones.*' => 'array:phone,type,comment',
            'phones.*.phone' => [
                'required',
                'integer',
                'digits_between:9,13',
                'regex:/^[0-9]+$/',
            ],
            'phones.*.type' => [
                'required',
                'integer',
                Rule::in(PhoneTypeEnum::values()),
            ],
            'phones.*.comment' => [
                'nullable',
                'string',
                'max:128',
            ],
            'addresses' => [
                'nullable',
                'array',
            ],
            'addresses.*' => 'array:type,address,region_id,district_id,mahalla_id',
            'addresses.*.type' => [
                'required',
                'integer',
                Rule::in(AddressTypeEnum::values()),
            ],
            'addresses.*.address' => [
                'required',
                'string',
                'max:255',
            ],
            'addresses.*.region_id' => [
                'nullable',
                'integer',
                Rule::exists('regions', 'id'),
            ],
            'addresses.*.district_id' => [
                'nullable',
                'integer',
                Rule::exists('districts', 'id'),
            ],
            'addresses.*.mahalla_id' => [
                'nullable',
                'integer',
                Rule::exists('mahallas', 'id'),
            ],
        ];
    }
}
