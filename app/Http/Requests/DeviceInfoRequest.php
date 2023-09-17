<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema()
 *
 * @property string $device_type
 * @property string $device_name
 * @property string $device_ip
 * @property string $location
 * @property string $app_version
 */

class DeviceInfoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @OA\Property(property="device_type", type="string", example="IOS / Android")
     * @OA\Property(property="device_name", type="string", example="Google Pixel 7")
     * @OA\Property(property="device_ip", type="string", example="127.0.0.1")
     * @OA\Property(property="location", type="string", example="Tashkent")
     * @OA\Property(property="app_version", type="string", example="1.0.7")
     *
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'device_type' => 'required|string|max:40',
            'device_name' => 'required|string|max:40',
            'device_ip' => 'required|string|max:40',
            'location' => 'required|string|max:40',
            'app_version' => 'required|string|max:40'
        ];
    }

}