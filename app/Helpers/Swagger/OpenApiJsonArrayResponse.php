<?php

namespace App\Helpers\Swagger;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class OpenApiJsonArrayResponse extends OA\Response
{
    public function __construct(string $resourceName, $description = null, $withPagination = false, $withSortables = false)
    {
        $properties = [
            new OA\Property(property: 'success', type: 'boolean', example: 'true'),
            new OA\Property(property: 'message', type: 'string', example: 'null'),
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/' . $resourceName)),
        ];

        if ($withSortables) {
            $properties[] = new OA\Property(property: 'sortables', type: 'array', items: new OA\Items(type: 'string', example: 'property_name'));
        }

        if ($withPagination) {
            $properties[] = new OA\Property(property: 'links', properties: [
                new OA\Property(property: 'first', type: 'string', example: 'http://revo.avtomato.uz/api/v1/management/branches?page=1'),
                new OA\Property(property: 'last', type: 'string', example: 'http://revo.avtomato.uz/api/v1/management/branches?page=1'),
                new OA\Property(property: 'prev', type: 'string', example: 'null'),
                new OA\Property(property: 'next', type: 'string', example: 'null'),
            ], type: 'object');
            $properties[] = new OA\Property(property: 'meta', properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: '1'),
                new OA\Property(property: 'from', type: 'integer', example: '1'),
                new OA\Property(property: 'last_page', type: 'integer', example: '1'),
                new OA\Property(property: 'path', type: 'string', example: 'http://revo.avtomato.uz/api/v1/management/branches'),
                new OA\Property(property: 'per_page', type: 'integer', example: '15'),
                new OA\Property(property: 'to', type: 'integer', example: '1'),
                new OA\Property(property: 'total', type: 'integer', example: '1'),
                new OA\Property(property: 'links', type: 'array', items: new OA\Items(properties: [
                    new OA\Property(property: 'url', type: 'string', example: 'http://revo.avtomato.uz/api/v1/management/branches?page=1'),
                    new OA\Property(property: 'label', type: 'string', example: '1'),
                    new OA\Property(property: 'active', type: 'boolean', example: 'true'),
                ], type: 'object'))
            ], type: 'object');
        }

        parent::__construct(
            response: '200',
            description: $description ?? 'Success',
            content: new OA\JsonContent(
                properties: $properties,
            )
        );
    }
}
