<?php

namespace App\Http\Controllers\PartnerApi\V1\Info;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Common\DistrictResource;
use App\Http\Resources\Common\MahallaResource;
use App\Http\Resources\Common\RegionResource;
use App\Http\Resources\SettingsResource;
use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    #[OA\Get(
        path: '/api/v1/info/regions',
        description: 'Regions list route',
        tags: ['Info'],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'RegionResource'),
        ]
    )]
    public function regions(): JsonResponse
    {
        return ApiResponse::success(RegionResource::collection(Region::query()->get()));
    }

    #[OA\Get(
        path: '/api/v1/info/districts',
        description: 'Districts list route',
        tags: ['Info'],
        parameters: [
            new OA\Parameter(name: 'region_id', description: 'Parent region id to filter', in: 'query', required: false),
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'DistrictResource'),
        ]
    )]
    public function districts(Request $request): JsonResponse
    {
        return ApiResponse::success(
            DistrictResource::collection(
                District::query()
                    ->when($request->get('region_id'), fn ($query) => $query->where('region_id', $request->get('region_id')))
                    ->get()
            )
        );
    }

    #[OA\Get(
        path: '/api/v1/info/mahallas',
        description: 'Mahalla list route',
        tags: ['Info'],
        parameters: [
            new OA\Parameter(name: 'district_id', description: 'Parent district id to filter', in: 'query', required: false),
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'MahallaResource'),
        ]
    )]
    public function mahallas(Request $request): JsonResponse
    {
        return ApiResponse::success(
            MahallaResource::collection(
                Mahalla::query()
                    ->when($request->get('district_id'), fn ($query) => $query->where('district_id', $request->get('district_id')))
                    ->get()
            )
        );
    }
}
