<?php

namespace App\Http\Controllers\PartnerApi\V1;

use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\SettingsResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class InfoController extends Controller
{
    #[OA\Get(
        path: '/api/v1/info/settings',
        description: 'Settings route',
        tags: ['Info'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SettingsResource'),
        ]
    )]
    public function settings(): JsonResponse
    {
        return ApiResponse::success(SettingsResource::make([]));
    }
}
