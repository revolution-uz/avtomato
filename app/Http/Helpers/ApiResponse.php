<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema()
 * @OA\Property(property="success", type="boolean", example="true")
 * @OA\Property(property="message", type="boolean", example="OK")
 * @OA\Property(property="data", type="object", example="null")
 */
class ApiResponse
{
    /**
     * @param  mixed  $data
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public static function success(mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => is_string($data) ? $data : null,
            'data' => is_string($data) ? null : $data,
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