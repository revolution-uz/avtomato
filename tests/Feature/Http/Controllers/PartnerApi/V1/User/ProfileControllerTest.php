<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\User;

use App\Enums\Company\AttachmentTypeEnum;
use App\Models\Company\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    protected bool $tenancy = true;

    /**
     * @test
     */
    public function me()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('profile.me'));

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
        $response = $this->actingAs($admin)->putJson(route('profile.update'), [
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
        $response = $this->actingAs($admin)->postJson(route('profile.change-password'), [
            'old_password' => 'password',
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
        $response = $this->actingAs($admin)->postJson(route('profile.change-theme'), [
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
        $response = $this->actingAs($admin)->postJson(route('profile.change-language'), [
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
        $response = $this->actingAs($admin)->postJson(route('profile.change-avatar'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk();
        $this->assertFileExists(storage_path('app/public/' . Attachment::UPLOADS_PATH . '/' . AttachmentTypeEnum::AVATAR->getStoragePath() . '/' . $admin->fresh()->avatar->path));
    }

    /**
     * @test
     */
    public function devices()
    {
        $admin = $this->createAdminUser();
        $admin->createTokenWithData('test');

        $response = $this->actingAs($admin)->getJson(route('profile.devices'));

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

        $response = $this->actingAs($admin)->deleteJson(route('profile.revoke-device'), ['ids' => [$token->id]]);

        $response->assertOk();
        $this->assertEmpty($admin->fresh()->tokens);
    }
}
