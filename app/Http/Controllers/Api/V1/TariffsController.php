<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\TariffsRequest;
use App\Http\Resources\TariffsResource;
use App\Models\Company\Tariffs;
use Illuminate\Http\JsonResponse;

class TariffsController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(TariffsResource::collection(Tariffs::all()));
    }

    public function get(int $id): JsonResponse
    {
        return ApiResponse::success(TariffsResource::make(Tariffs::find($id)));
    }

    public function create(TariffsRequest $request): JsonResponse
    {
        $model = new Tariffs();
        $model->name = $request->name;
        $model->is_annual = $request->is_annual;
        $model->percent = $request->percent;
        $model->min_amount = $request->min_amount;
        $model->max_amount = $request->max_amount;
        $model->min_initial_fee = $request->min_initial_fee;
        $model->max_initial_fee = $request->max_initial_fee;
        if ($model->save())
            return ApiResponse::success(TariffsResource::make($model));
        return ApiResponse::error("Error on creating Tariffs");
    }

    public function update(int $id, TariffsRequest $request): JsonResponse
    {
        $model = Tariffs::find($id);
        $model->name = $request->name;
        $model->is_annual = $request->is_annual;
        $model->percent = $request->percent;
        $model->min_amount = $request->min_amount;
        $model->max_amount = $request->max_amount;
        $model->min_initial_fee = $request->min_initial_fee;
        $model->max_initial_fee = $request->max_initial_fee;
        if ($model->save())
            return ApiResponse::success(TariffsResource::make($model));
        return ApiResponse::error("Error on updating Tariffs");
    }

    public function delete(Tariffs $tariffs): JsonResponse
    {
        return ApiResponse::success($tariffs->delete());
    }
}
