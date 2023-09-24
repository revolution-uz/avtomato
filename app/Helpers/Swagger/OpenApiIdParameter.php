<?php

namespace App\Helpers\Swagger;

use OpenApi\Attributes as OA;
class OpenApiIdParameter extends OA\Parameter
{
    public function __construct(string $modelName)
    {
        parent::__construct(
            name: 'id',
            description: $modelName . ' ID',
            in: 'path',
            required: true
        );
    }
}
