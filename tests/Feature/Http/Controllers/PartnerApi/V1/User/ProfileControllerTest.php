<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\User;

use App\Models\Company\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function me()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('profile.me'));

        $response->assertOk();
        $this->assertEquals($admin->id, $response->json('data.id'));
        $this->assertEquals($admin->first_name, $response->json('data.first_name'));
        $this->assertEquals($admin->last_name, $response->json('data.last_name'));
        $this->assertEquals($admin->username, $response->json('data.username'));
    }

    /**
     * @test
     */
    public function update()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->put(route('profile.update'), [
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
        ]);

        $response->assertOk();
        $this->assertEquals('Updated First Name', $admin->first_name);
        $this->assertEquals('Updated Last Name', $admin->last_name);
    }

    /**
     * @test
     */
    public function changePassword()
    {
        $admin = $this->createAdminUser();
        $newPassword = 'password';
        $response = $this->actingAs($admin)->post(route('profile.change-password'), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        $admin->refresh();
        $response->assertOk();
        $this->assertTrue(Hash::check($newPassword, $admin->password));
    }

    /**
     * @test
     */
    public function changeTheme()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->post(route('profile.change-theme'), [
            'layout_name' => 'fixed',
        ]);

        $response->assertOk();
        $this->assertEquals('fixed', $response->json('data.template_theme.layout_name'));
        $this->assertEquals('fixed', $admin->fresh()->template_theme->layout_name);
    }

    /**
     * @test
     */
    public function changeLanguage()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->post(route('profile.change-language'), [
            'language' => 'uz',
        ]);

        $response->assertOk();
        $this->assertEquals('uz', $response->json('data.language'));
        $this->assertEquals('uz', $admin->fresh()->language);
    }

    /**
     * @test
     */
    public function changeAvatar()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->post(route('profile.change-avatar'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk();
        $file_name = $admin->fresh()->avatar;
        $this->assertFileExists(storage_path('app/public/' . User::AVATAR_PATH . '/' . $file_name));
        $this->assertFileExists(storage_path('app/public/' . User::THUMBNAIL_PATH . '/' . $file_name));
    }

    /**
     * @test
     */
    public function devices()
    {
        $admin = $this->createAdminUser();
        $admin->createTokenWithData('test');

        $response = $this->actingAs($admin)->get(route('profile.devices'));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * @test
     */
    public function revokeDevice()
    {
        $admin = $this->createAdminUser();
        $admin->createTokenWithData('test');
        $token = $admin->tokens()->first();

        $response = $this->actingAs($admin)->delete(route('profile.revoke-device', ['id' => $token->id]));

        $response->assertOk();
        $this->assertEmpty($admin->fresh()->tokens);
    }
}
