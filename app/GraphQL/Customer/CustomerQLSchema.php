<?php

namespace App\GraphQL\Customer;

use App\GraphQL\Customer\Queries\CustomersGraphQLQuery;
use App\GraphQL\Customer\Queries\SingleCustomerGraphQLQuery;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\GraphQL\Info\Types\DistrictGraphQLType;
use App\GraphQL\Info\Types\MahallaGraphQLType;
use App\GraphQL\Info\Types\RegionGraphQLType;
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
                //                CreateCustomerGraphQLMutation::class,
                //                UpdateCustomerGraphQLMutation::class,
            ],
            'types' => [
                AddressGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                PhoneGraphQLType::class,
                CustomerGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
