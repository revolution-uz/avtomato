<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Customer;

use App\Enums\GenderEnum;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use Tests\Fakers\AtmosFaker;
use Tests\Fakers\PlumFaker;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    protected bool $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('customer.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function single()
    {
        $customer = Customer::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('customer.get-single', ['id' => $customer->id]));

        $response->assertOk();
        $this->assertEquals($customer->first_name, $response->json('data.first_name'));
    }

    /**
     * @test
     */
    public function infoByPassport()
    {
        $request = [
            'passport_series' => 'AA',
            'passport_number' => '1234567',
            'birth_date' => '1996-01-01',
        ];
        $name = 'Ali';
        PlumFaker::get_info_by_passport($name);
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('customer.info-by-passport', $request));

        $response->assertOk();
        $this->assertEquals($name, $response->json('data.first_name'));
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
        $response = $this->actingAs($admin)->postJson(route('customer.create'), $request);

        $response->assertSuccessful();
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

    /**
     * @test
     */
    public function update()
    {
        $customer = Customer::factory()->create();

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
        ];

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->putJson(route('customer.update', ['id' => $customer->id]), $request);
        $customer->refresh();

        $response->assertSuccessful();
        $this->assertEquals($request['first_name'], $response->json('data.first_name'));
        $this->assertDatabaseHas('customers', ['pinfl' => $request['pinfl']]);
        $this->assertEquals($customer->last_name, $request['last_name']);
    }

    /**
     * @test
     */
    public function attachCard()
    {
        $customer = Customer::factory()->create();
        AtmosFaker::attachCard();

        $request = [
            'number' => '1234567890123456',
            'expiry' => '2301',
        ];

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('customer.attach-card', ['id' => $customer->id]), $request);

        $response->assertSuccessful();
        $this->assertDatabaseHas('cards', ['pan' => '123456******3456']);
    }

    /**
     * @test
     */
    public function detachCard()
    {
        $customer = Customer::factory()->create();
        $card = Card::factory()->create([
            'customer_id' => $customer->id,
            'external_id' => null,
        ]);

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->deleteJson(route('customer.detach-card', ['id' => $customer->id]), ['ids' => [$card->id]]);

        $response->assertSuccessful();
        $this->assertEmpty($customer->fresh()->cards);
        $this->assertNotNull($card->fresh()->deleted_at);
    }

    /**
     * @test
     */
    public function confirmCard()
    {
        $customer = Customer::factory()->create();
        $card = Card::factory()->create([
            'customer_id' => $customer->id,
            'external_id' => null,
        ]);

        $card_id = '1234567890';
        AtmosFaker::confirmCard();

        $request = [
            'card_id' => $card->id,
            'transaction_id' => 12345,
            'otp' => '123456',
        ];

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('customer.confirm-card', ['id' => $customer->id]), $request);

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function attachAddress()
    {
        $customer = Customer::factory()->create();

        $request = [
            'type' => 1,
            'address' => 'Test address',
            'region_id' => 1,
            'district_id' => 1,
            'mahalla_id' => 1,
        ];

        $this->seedAddress();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('customer.attach-address', ['id' => $customer->id]), $request);

        $response->assertSuccessful();
        $this->assertDatabaseHas('addresses', ['address' => $request['address']]);
        $this->assertCount(1, $customer->fresh()->addresses);
    }

    /**
     * @test
     */
    public function detachAddress()
    {
        $customer = Customer::factory()->create();
        $this->seedAddress();
        $address = $customer->addresses()->create([
            'type' => 1,
            'address' => 'Test address',
            'region_id' => 1,
            'district_id' => 1,
            'mahalla_id' => 1,
        ]);

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->deleteJson(route('customer.detach-address', ['id' => $customer->id]), ['ids' => [$address->id]]);

        $response->assertSuccessful();
        $this->assertEmpty($customer->fresh()->addresses);
        $this->assertNotNull($address->fresh()->deleted_at);
    }

    /**
     * @test
     */
    public function attachPhone()
    {
        $customer = Customer::factory()->create();

        $request = [
            'phone' => '998901234567',
            'type' => 1,
            'comment' => 'Test comment',
        ];

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('customer.attach-phone', ['id' => $customer->id]), $request);

        $response->assertSuccessful();
        $this->assertDatabaseHas('phones', ['phone' => $request['phone']]);
        $this->assertCount(1, $customer->fresh()->phones);
    }
}
