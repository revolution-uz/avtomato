<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\User;

use App\Models\Company\PersonalAccessToken;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    protected $tenancy = true;

    public function testMe()
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->get(route('profile.me'));

        $response->assertOk();
        $this->assertEquals($user->id, $response->json('data.id'));
        $this->assertEquals($user->first_name, $response->json('data.first_name'));
        $this->assertEquals($user->last_name, $response->json('data.last_name'));
        $this->assertEquals($user->username, $response->json('data.username'));
    }

    public function testUpdate()
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->put(route('profile.update'), [
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
        ]);

        $response->assertOk();
        $this->assertEquals('Updated First Name', $response->json('data.first_name'));
        $this->assertEquals('Updated Last Name', $response->json('data.last_name'));
    }

    public function testChangePassword()
    {
        $user = $this->createAdminUser();
        $newPassword = 'password';
        $response = $this->actingAs($user, 'web')->post(route('profile.change-password'), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertOk();
        // todo fix this
//        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    public function testChangeTheme()
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->post(route('profile.change-theme'), [
            'layout_name' => 'fixed',
        ]);

        $response->assertOk();
        $this->assertEquals('fixed', $response->json('data.template_theme.layout_name'));
    }

    public function testChangeLanguage()
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->post(route('profile.change-language'), [
            'language' => 'uz',
        ]);

        $response->assertOk();
        $this->assertEquals('uz', $response->json('data.language'));
    }

    public function testDevices()
    {
        $user = $this->createAdminUser();
        $user->createTokenWithDeviceName('test', 'localhost');

        $response = $this->actingAs($user, 'web')->get(route('profile.devices'));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

//    public function testRevokeDevice()
//    {
//        $user = $this->createAdminUser();
//        $token = $user->createTokenWithDeviceName('test', 'localhost');
//
//        $response = $this->actingAs($user, 'web')->delete(route('profile.revoke-device', $token));
//
//        $response->assertOk();
//        $this->assertCount(0, PersonalAccessToken::all());
//    }
}
