<?php

namespace App\GraphQL\Marketing;

use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class MarketingGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                \App\GraphQL\Marketing\Queries\SmsTemplatesGraphQLQuery::class,
            ],
            'mutation' => [
                //
            ],
            'types' => [
                \App\GraphQL\Marketing\Types\SmsTemplateGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin'],
        ];
    }
}
