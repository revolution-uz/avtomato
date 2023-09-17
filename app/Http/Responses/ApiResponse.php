<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * Class for common API functions
 */
class ApiResponse extends JsonResource
{
    /**
     * @param $data
     * @param $additional_info
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public static function success($data = false, $additional_info = false)
    {
        $response = [];
        $response['status'] = 1;
        if ($data !== false)
            $response['data'] = $data;
        if ($additional_info) {
            $response = array_merge($response, $additional_info);
        }
        return self::makeResponse($response, 200);
    }

    /**
     * @param $message
     * @param $statusCode
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public static function failed($message = false, $statusCode = 400)
    {
        $response = [];
        $response['status'] = 0;
        if ($message !== false)
            $response['message'] = $message;
        return self::makeResponse($response, $statusCode);
    }

    /**
     * @param $data
     * @param $statusCode
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    private static function makeResponse($data, $statusCode)
    {
        return response($data, $statusCode);
    }

}
