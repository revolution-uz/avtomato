<?php

namespace App\Helpers\Swagger;

use OpenApi\Attributes as OA;

class OpenApiPageParameter extends OA\Parameter
{
    public function __construct()
    {
        parent::__construct(
            name: 'page',
            description: 'Page number',
            in: 'path',
            required: false
        );
    }
}
