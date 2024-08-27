<?php

namespace Tests\GraphQL;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

abstract class GraphQLTestCase extends TestCase
{
    protected bool $tenancy = true;

    protected function getGraphQLType($variable): string
    {
        if (is_int($variable)) {
            return 'Int!';
        }
        if (is_float($variable)) {
            return 'Float!';
        }
        if (is_bool($variable)) {
            return 'Boolean!';
        }
        if (is_string($variable)) {
            return 'String!';
        }

        return 'Object';
    }

    protected function mockUrl($url, $response, $status = 200): void
    {
        Http::fake([
            $url => Http::response(
                $response,
                $status, // Status code
                ['Content-Type' => 'application/json']
            ),
        ]);
    }

    protected function convertIntoQuery(array $structure): string
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if ($key == '*') {
                    $structure[$key] = $this->convertIntoQuery($value);
                } else {
                    $structure[$key] = "{$key} {{$this->convertIntoQuery($value)}}";
                }
            }
        }

        return implode(' ', $structure);
    }
}
