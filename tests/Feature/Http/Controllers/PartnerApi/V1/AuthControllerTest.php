<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1;

use App\Models\Company\CompanyAdmin;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function login()
    {
        $admin = CompanyAdmin::factory()->create([
            'username' => 'test',
            'password' => bcrypt('password'),
        ]);
        $response = $this->postJson(route('auth.login'), [
            'username' => $admin->username,
            'password' => 'password',
        ], ['Device-Name' => 'test', 'Device-OS' => 'android', 'Device-Type' => 'mobile']);

        $response->assertOk();
        $this->assertIsString($response->json('data.token'));
    }
}
