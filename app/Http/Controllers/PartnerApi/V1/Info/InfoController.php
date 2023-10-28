<?php

namespace App\Http\Controllers\PartnerApi\V1\Info;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Info\InfoDistrictRequest;
use App\Http\Requests\Info\InfoMahallaRequest;
use App\Http\Requests\Info\InfoRegionsRequest;
use App\Http\Requests\Info\InfoSettingsRequest;
use App\Http\Resources\Common\DistrictResource;
use App\Http\Resources\Common\MahallaResource;
use App\Http\Resources\Common\RegionResource;
use App\Http\Resources\SettingsResource;
use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class InfoController extends Controller
{
    #[OA\Get(
        path: '/api/v1/info/settings',
        description: 'Settings route',
        tags: ['Info'],
        responses: [
            new OpenApiDataResponse(resourceName: 'SettingsResource'),
        ]
    )]
    public function settings(InfoSettingsRequest $request): SettingsResource
    {
        return new SettingsResource([]);
    }

    #[OA\Get(
        path: '/api/v1/info/regions',
        description: 'Regions list route',
        tags: ['Info'],
        responses: [
            new OpenApiArrayResponse(resourceName: 'RegionResource'),
        ]
    )]
    public function regions(InfoRegionsRequest $request): AnonymousResourceCollection
    {
        return RegionResource::collection(Region::query()->get());
    }

    #[OA\Get(
        path: '/api/v1/info/districts',
        description: 'Districts list route',
        tags: ['Info'],
        parameters: [
            new OA\Parameter(name: 'region_id', description: 'Parent region id to filter', in: 'query', required: false),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'DistrictResource'),
        ]
    )]
    public function districts(InfoDistrictRequest $request): AnonymousResourceCollection
    {
        return DistrictResource::collection(
            District::query()
                ->when($request->get('region_id'), fn ($query) => $query->where('region_id', $request->get('region_id')))
                ->get()
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
            new OpenApiArrayResponse(resourceName: 'MahallaResource'),
        ]
    )]
    public function mahallas(InfoMahallaRequest $request): AnonymousResourceCollection
    {
        return MahallaResource::collection(
            Mahalla::query()
                ->when($request->get('district_id'), fn ($query) => $query->where('district_id', $request->get('district_id')))
                ->get()
        );
    }
}
