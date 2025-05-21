<?php

namespace App\GraphQL\Customer\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\AddressInputGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Customer\Types\PhoneInputGraphQLType;
use App\Helpers\RbacHelper;
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

    public function args(): array
    {
        return [
            'customer' => [
                'name' => 'customer',
                'type' => Type::nonNull(GraphQL::type(CustomerInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
            'phones' => [
                'name' => 'phones',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(PhoneInputGraphQLType::GRAPHQL_TYPE_NAME)))),
            ],
            'addresses' => [
                'name' => 'addresses',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(AddressInputGraphQLType::GRAPHQL_TYPE_NAME)))),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        DB::beginTransaction();

        try {
            $customerService = new CustomerService;
            $customer = $customerService->saveCustomer($args['customer']);

            foreach ($args['phones'] as $phoneInput) {
                /** @var Phone $phone */
                $phone = Phone::query()->firstOrCreate(['phone' => $phoneInput['phone']]);

                $customer->phones()->attach($phone->id, ['type' => $phoneInput['type'], 'comment' => $phoneInput['comment']]);
            }

            $customer->addresses()->createMany($args['addresses']);

            DB::commit();

        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $customer;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'customer.pinfl' => Rule::unique('customers', 'pinfl'),
            'customer.passport_number' => Rule::unique('customers', 'passport_number')->where('passport_series', $args['customer']['passport_series']),
        ]);
    }
}
