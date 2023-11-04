<?php

namespace App\Helpers\Swagger;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class OpenApiDataResponse extends OA\Response
{
    public function __construct(string $resourceName, $response = '200', $description = null, $nullable = false)
    {
        parent::__construct(
            response: $response,
            description: $description ?? 'Success',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', ref: '#/components/schemas/' . $resourceName, nullable: $nullable),
                ],
            )
        );
    }
}
