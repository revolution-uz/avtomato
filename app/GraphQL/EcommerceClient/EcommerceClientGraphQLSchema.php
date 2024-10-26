<?php

namespace App\GraphQL\EcommerceClient;

use App\GraphQL\EcommerceClient\Mutation\LoginGraphQLMutation;
use App\GraphQL\EcommerceClient\Mutation\RegisterGraphQLMutation;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
            ],
            'mutation' => [
                RegisterGraphQLMutation::class,
                LoginGraphQLMutation::class,
            ],
            'types' => [

            ],
            'middleware' => ['optional_auth:sanctum'],
        ];
    }
}
