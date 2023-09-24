<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Company\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: '/api/v1/users',
        description: 'Users route',
        tags: ['Users'],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'UserResource'),
        ]
    )]
    public function index(): JsonResponse
    {
        return ApiResponse::success(UserResource::collection(User::all()));
    }

    #[OA\Get(
        path: '/api/v1/users/{id}',
        description: 'Users get single route',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'UserResource'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function getSingle(User $user): JsonResponse
    {
        return ApiResponse::success(UserResource::make($user));
    }

    #[OA\Post(
        path: '/api/v1/users',
        description: 'Users create route',
        requestBody: new OA\RequestBody(
            description: 'Users create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserRequest')
        ),
        tags: ['Users'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'UserResource'),
        ]
    )]
    public function create(UserRequest $request): JsonResponse
    {
        $model = new User();
        $model->fill($request->validated());
        $model->password = bcrypt($request->password);

        if ($model->save())
            return ApiResponse::success(UserResource::make($model));

        return ApiResponse::error("Error on creating Users");
    }

    #[OA\Put(
        path: '/api/v1/users/{id}',
        description: 'Users create route',
        requestBody: new OA\RequestBody(
            description: 'Users create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserRequest')
        ),
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'UserResource'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function update(User $user, UserRequest $request): JsonResponse
    {
        $user->fill($request->validated());
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($user->save()) {
            return ApiResponse::success(UserResource::make($user));
        }

        return ApiResponse::error("Error on updating Users");
    }

    #[OA\Delete(
        path: '/api/v1/users/{id}',
        description: 'Users delete route',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(User $user): JsonResponse
    {
        $user->deleteOrFail();

        return ApiResponse::success(true);
    }
}
