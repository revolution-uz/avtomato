<?php

namespace App\GraphQL\Customer;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Mutations\AttachAddressGraphQLMutation;
use App\GraphQL\Customer\Mutations\AttachCardGraphQLMutation;
use App\GraphQL\Customer\Mutations\AttachPhoneGraphQLMutation;
use App\GraphQL\Customer\Mutations\ConfirmCardGraphQLMutation;
use App\GraphQL\Customer\Mutations\CreateCustomerGraphQLMutation;
use App\GraphQL\Customer\Mutations\DetachAddressGraphQLMutation;
use App\GraphQL\Customer\Mutations\DetachCardGraphQLMutation;
use App\GraphQL\Customer\Mutations\DetachPhoneGraphQLMutation;
use App\GraphQL\Customer\Mutations\UpdateCustomerGraphQLMutation;
use App\GraphQL\Customer\Queries\CustomerByPassportGraphQLQuery;
use App\GraphQL\Customer\Queries\CustomersGraphQLQuery;
use App\GraphQL\Customer\Queries\InfoByPassportGraphQLQuery;
use App\GraphQL\Customer\Queries\SingleCustomerGraphQLQuery;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\AddressInputGraphQLType;
use App\GraphQL\Customer\Types\AttachCardDataGraphQLType;
use App\GraphQL\Customer\Types\AttachCardGraphQLType;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Customer\Types\PassportDataGraphQLType;
use App\GraphQL\Customer\Types\PhoneGraphQLType;
use App\GraphQL\Customer\Types\PhoneInputGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class CustomerQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CustomersGraphQLQuery::class,
                SingleCustomerGraphQLQuery::class,
                InfoByPassportGraphQLQuery::class,
                CustomerByPassportGraphQLQuery::class,
            ],
            'mutation' => [
                AttachAddressGraphQLMutation::class,
                AttachCardGraphQLMutation::class,
                AttachPhoneGraphQLMutation::class,
                ConfirmCardGraphQLMutation::class,
                CreateCustomerGraphQLMutation::class,
                DetachAddressGraphQLMutation::class,
                DetachCardGraphQLMutation::class,
                DetachPhoneGraphQLMutation::class,
                UpdateCustomerGraphQLMutation::class,
            ],
            'types' => [
                AddressGraphQLType::class,
                AddressInputGraphQLType::class,
                AttachCardDataGraphQLType::class,
                AttachCardGraphQLType::class,
                CardGraphQLType::class,
                CustomerGraphQLType::class,
                CustomerInputGraphQLType::class,
                PhoneGraphQLType::class,
                PhoneInputGraphQLType::class,
                RegionGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                PassportDataGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
