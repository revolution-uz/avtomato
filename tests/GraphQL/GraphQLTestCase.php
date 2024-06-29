<?php

namespace Tests\GraphQL;

use Tests\TestCase;

abstract class GraphQLTestCase extends TestCase
{
    protected bool $tenancy = true;

    protected function getGraphQLType($variable): string
    {
        if (is_int($variable)) {
            return 'Int';
        }
        if (is_float($variable)) {
            return 'Float';
        }
        if (is_bool($variable)) {
            return 'Boolean';
        }
        if (is_string($variable)) {
            return 'String';
        }
        if (is_string($variable)) {
            return 'String';
        }

        return 'Object';
    }
}
