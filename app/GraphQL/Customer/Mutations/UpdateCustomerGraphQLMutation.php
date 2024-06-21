<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\GenderEnum;
use App\GraphQL\Common\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\Helpers\RbacHelper;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Services\CustomerService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateCustomerGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCustomer',
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
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $request = new CreateCustomerRequest($args);
        $this->customerService->saveCustomer($request, $args['id']);

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
                    ->where('passport_series', $args['passport_series']),
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
        ];
    }
}
