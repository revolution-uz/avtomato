<?php

namespace App\Helpers\Swagger;

use OpenApi\Attributes as OA;

class OpenApiSortParameter extends OA\Parameter
{
    public function __construct()
    {
        parent::__construct(
            name: 'sort',
            description: 'Field name to sort. If you put minus sign prefix it means descending order. Example: -name',
            in: 'query',
            required: false
        );
    }
}
