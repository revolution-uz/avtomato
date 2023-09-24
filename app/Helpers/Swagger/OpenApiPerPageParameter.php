<?php

namespace App\Helpers\Swagger;

use OpenApi\Attributes as OA;

class OpenApiPerPageParameter extends OA\Parameter
{
    public function __construct()
    {
        parent::__construct(
            name: 'per_page',
            description: 'Limit items per page (default 10)',
            in: 'query',
            required: false
        );
    }
}
