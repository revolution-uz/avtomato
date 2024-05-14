<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\AddressTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachAddressRequest',
    title: 'Request to create an address',
    properties: [
        new OA\Property(property: 'type', type: 'int', enum: [AddressTypeEnum::BIRTH_PLACE, AddressTypeEnum::LIVING_PLACE, AddressTypeEnum::TEMPORARY_PLACE, AddressTypeEnum::WORK_PLACE], example: '1'),
        new OA\Property(property: 'address', type: 'string', example: 'Al-Xorazmiy ko\'chasi 1-uy'),
        new OA\Property(property: 'region_id', type: 'int', example: '1', nullable: true),
        new OA\Property(property: 'district_id', type: 'int', example: '2', nullable: true),
        new OA\Property(property: 'mahalla_id', type: 'int', example: '3', nullable: true),
    ],
)]
/**
 * @property int $type
 * @property string $address
 * @property int|null $region_id
 * @property int|null $district_id
 * @property int|null $mahalla_id
 */
class AttachAddressRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|int|in:' . implode(',', AddressTypeEnum::values()),
            'address' => 'required|string|max:128',
            'region_id' => 'nullable|int|exists:regions,id',
            'district_id' => 'nullable|int|exists:districts,id',
            'mahalla_id' => 'nullable|int|exists:mahallas,id',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }
}
