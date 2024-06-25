<?php

namespace App\GraphQL\Customer;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Mutations\CreateCustomerGraphQLMutation;
use App\GraphQL\Customer\Mutations\UpdateCustomerGraphQLMutation;
use App\GraphQL\Customer\Queries\CustomersGraphQLQuery;
use App\GraphQL\Customer\Queries\SingleCustomerGraphQLQuery;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class CustomerQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CustomersGraphQLQuery::class,
                SingleCustomerGraphQLQuery::class,
            ],
            'mutation' => [
                CreateCustomerGraphQLMutation::class,
                UpdateCustomerGraphQLMutation::class,
            ],
            'types' => [
                AddressGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                PhoneGraphQLType::class,
                CustomerGraphQLType::class,
                CardGraphQLType::class,
                CustomerInputGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
