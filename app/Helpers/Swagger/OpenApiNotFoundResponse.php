<?php

namespace App\Helpers\Swagger;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class OpenApiNotFoundResponse extends OA\Response
{
    public function __construct($message = null)
    {
        parent::__construct(
            response: '404',
            description: 'Not found',
            content: new OA\JsonContent(
                example: [
                    'status' => 404,
                    'message' => $message ?? 'Not found',
                ]
            )
        );
    }
}
