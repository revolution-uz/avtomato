<?php

namespace App\Helpers\Swagger;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class OpenApiErrorResponse extends OA\Response
{
    public function __construct($message, $description = null)
    {
        parent::__construct(
            response: '201', // it's trick, because swagger doesn't accept multiple 200 responses
            description: $description ?? 'Error',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: 'false'),
                    new OA\Property(property: 'message', type: 'string', example: $message),
                    new OA\Property(property: 'data', example: 'null'),
                ],
            )
        );
    }
}
