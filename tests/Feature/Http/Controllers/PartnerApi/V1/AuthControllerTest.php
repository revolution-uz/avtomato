<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1;

use App\Models\Company\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function login()
    {
        $user = User::factory()->create([
            'username' => 'test',
            'password' => bcrypt('password'),
        ]);
        $response = $this->post(route('auth.login'), [
            'username' => $user->username,
            'password' => 'password',
            'device_name' => 'local',
        ]);

        $response->assertOk();
        $this->assertIsString($response->json('data'));
    }
}
