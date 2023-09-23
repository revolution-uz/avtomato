<?php

namespace App\Helpers\Swagger;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class OpenApiSingleDataResponse extends OA\Response
{
    public function __construct(string $data, string $type, $description = null)
    {
        parent::__construct(
            response: '200',
            description: $description ?? 'Success',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: 'true'),
                    new OA\Property(property: 'message', type: 'string', example: 'null'),
                    new OA\Property(property: 'data', type: $type, example: $data),
                ],
            )
        );
    }
}
