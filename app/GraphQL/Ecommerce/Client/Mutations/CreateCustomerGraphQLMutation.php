<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\CustomerTypeEnum;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\CustomerGraphQLType;
use App\Models\Company\Customer;
use App\Models\Company\Ecommerce\User;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateCustomerGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCustomer',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'customer' => [
                'name' => 'customer',
                'type' => Type::nonNull(GraphQL::type(CustomerInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        /** @var User $user */
        $user = auth('ecommerce-client')->user();

        $args['customer']['type'] = CustomerTypeEnum::INSTALLMENT_AND_ECOMMERCE;
        if ($customer = $user->customer) {
            $customer->updateOrFail($args['customer']);
        } else {
            $customer = Customer::query()->create($args['customer']);
            $user->customer()->associate($customer);
        }

        return $user->customer;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'customer.pinfl' => [
                'required',
                'digits:14',
                Rule::unique('customers', 'pinfl')
                    ->when(auth('ecommerce-client')->user()->customer_id, fn ($q) => $q
                        ->whereNot('id', auth('ecommerce-client')->user()->customer_id)),
            ],
            'customer.passport_number' => [
                'required',
                'string',
                'digits:7',
                Rule::unique('customers', 'passport_number')
                    ->where('passport_series', $args['customer']['passport_series'])
                    ->when(auth('ecommerce-client')->user()->customer_id, fn ($q) => $q
                        ->whereNot('id', auth('ecommerce-client')->user()->customer_id)),
            ],
        ]);
    }
}
