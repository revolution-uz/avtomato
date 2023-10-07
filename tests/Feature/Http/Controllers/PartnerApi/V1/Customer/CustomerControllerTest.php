<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Customer;

use App\Enums\GenderEnum;
use App\Models\Company\Customer;
use App\Models\Company\User;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    protected $tenancy = true;

    public function testSingle()
    {
//        $customer = Customer::factory()->create();
//
//        $user = $this->createAdminUser();
//        $response = $this->actingAs($user, 'web')->get(route('customer.get-single', ['id' => $customer->id]));
//
//        $response->assertOk();
//        $this->assertEquals($customer->first_name, $response->json('data.first_name'));
    }

    public function testCreate()
    {
//        $gender = GenderEnum::randomValue();
//        $request = [
//            'pinfl' => '12345678901234',
//            'tin' => '123456789',
//            'passport_series' => 'AA',
//            'passport_number' => '1234567',
//            'passport_given_date' => '2021-01-01',
//            'passport_expiration_date' => '2031-01-01',
//            'passport_given_place' => 'Urganch shahar',
//            'passport_citizenship' => 'Uzbekistan',
//            'first_name' => 'John',
//            'last_name' => 'Doe',
//            'middle_name' => 'Alex',
//            'birth_date' => '1996-01-01',
//            'birth_place' => 'Urganch shahar',
//            'gender' => $gender,
//            'phones' => [
//                [
//                    'phone' => '998901234567',
//                    'type' => 1,
//                    'comment' => 'Test comment',
//                ],
//            ],
//            'addresses' => [
//                [
//                    'type' => 1,
//                    'address' => 'Test address',
//                    'region_id' => 1,
//                    'district_id' => 1,
//                    'mahalla_id' => 1,
//                ],
//            ]
//        ];
//
//        $this->seedAddress();
//        $user = $this->createAdminUser();
//        $response = $this->actingAs($user, 'web')->post(route('customer.create'), $request);
//
//        $response->assertOk();
    }
}
