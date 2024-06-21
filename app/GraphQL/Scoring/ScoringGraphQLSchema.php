<?php

namespace App\GraphQL\Scoring;

use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class ScoringGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
            ],
            'mutation' => [
            ],
            'types' => [
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
