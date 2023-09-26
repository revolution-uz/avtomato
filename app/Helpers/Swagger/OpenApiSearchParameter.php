<?php

namespace App\Helpers\Swagger;

use OpenApi\Attributes as OA;

class OpenApiSearchParameter extends OA\Parameter
{
    public function __construct()
    {
        parent::__construct(
            name: 'search',
            description: 'Search term',
            in: 'query',
            required: false
        );
    }
}
