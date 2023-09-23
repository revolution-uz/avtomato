<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiResponse',
    title: 'ApiResponse',
    properties: [
        new OA\Property(property: 'success', type: 'bool', example: 'true'),
        new OA\Property(property: 'message', type: 'string', example: 'Error on something when success is false'),
        new OA\Property(property: 'data', type: 'any', example: 'Any response data'),
    ],
)]
class ApiResponse
{
    /**
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(mixed $data, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $data,
            'data' => null,
        ], $statusCode);
    }
}
