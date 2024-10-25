<?php

namespace App\GraphQL\EcommerceClient\Common;

use App\GraphQL\EcommerceClient\Common\Mutation\EcommerceLoginGraphQLMutation;
use App\GraphQL\EcommerceClient\Common\Mutation\EcommerceRegisterGraphQLMutation;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceCommonGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
            ],
            'mutation' => [
                EcommerceRegisterGraphQLMutation::class,
                EcommerceLoginGraphQLMutation::class,
            ],
            'types' => [

            ],
            'middleware' => ['optional_auth:sanctum'],
        ];
    }
}
