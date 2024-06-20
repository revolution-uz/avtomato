<?php

namespace App\GraphQL\Common;

use App\GraphQL\Common\Mutations\LoginGraphQLMutation;
use App\GraphQL\Common\Queries\SettingsGraphQLQuery;
use App\GraphQL\Common\Types\PhoneGraphQLType;
use App\GraphQL\Common\Types\SettingsGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class CommonGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                SettingsGraphQLQuery::class,
            ],
            'mutation' => [
                LoginGraphQLMutation::class,
            ],
            'types' => [
                SettingsGraphQLType::class,
                PhoneGraphQLType::class,
            ],
        ];
    }
}
