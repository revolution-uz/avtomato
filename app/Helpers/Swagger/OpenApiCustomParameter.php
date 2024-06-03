<?php

namespace App\Helpers\Swagger;

use OpenApi\Attributes as OA;

class OpenApiCustomParameter extends OA\Parameter
{
    public function __construct(string $name, string $in = 'query', bool $required = false)
    {
        parent::__construct(
            name: $name,
            description: 'This field for ' . $name . ' in ' . $in,
            in: $in,
            required: $required
        );
    }
}
