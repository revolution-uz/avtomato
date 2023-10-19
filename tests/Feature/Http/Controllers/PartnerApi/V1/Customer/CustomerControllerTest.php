<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Customer;

use App\Enums\GenderEnum;
use App\Models\Company\Customer;
use App\Models\Company\User;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('customer.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function indexForbidden()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('customer.index'));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function single()
    {
        $customer = Customer::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('customer.get-single', ['id' => $customer->id]));

        $response->assertOk();
        $this->assertEquals($customer->first_name, $response->json('data.first_name'));
    }

    /**
     * @test
     */
    public function create()
    {
        $gender = GenderEnum::randomValue();
        $request = [
            'pinfl' => '12345678901234',
            'tin' => '123456789',
            'passport_series' => 'AA',
            'passport_number' => '1234567',
            'passport_given_date' => '2021-01-01',
            'passport_expiration_date' => '2031-01-01',
            'passport_given_place' => 'Urganch shahar',
            'passport_citizenship' => 'Uzbekistan',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Alex',
            'birth_date' => '1996-01-01',
            'birth_place' => 'Urganch shahar',
            'gender' => $gender,
            'phones' => [
                [
                    'phone' => '998901234567',
                    'type' => 1,
                    'comment' => 'Test comment',
                ],
            ],
            'addresses' => [
                [
                    'type' => 1,
                    'address' => 'Test address',
                    'region_id' => 1,
                    'district_id' => 1,
                    'mahalla_id' => 1,
                ],
            ],
        ];

        $this->seedAddress();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->post(route('customer.create'), $request);

        $response->assertOk();
        $this->assertEquals($request['first_name'], $response->json('data.first_name'));
        $this->assertDatabaseHas('customers', ['pinfl' => $request['pinfl']]);
        $this->assertDatabaseHas('phones', ['phone' => $request['phones'][0]['phone']]);
        $this->assertDatabaseHas('addresses', ['address' => $request['addresses'][0]['address']]);

        $customer = Customer::findOrFail($response->json('data.id'));
        $this->assertCount(1, $customer->phones);
        $this->assertCount(1, $customer->addresses);
        $this->assertEquals($request['phones'][0]['phone'], $customer->phones[0]->phone);
        $this->assertEquals($request['addresses'][0]['address'], $customer->addresses[0]->address);
    }
}
