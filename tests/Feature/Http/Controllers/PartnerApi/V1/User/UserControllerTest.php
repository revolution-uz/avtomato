<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\User;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\UserStatusEnum;
use App\Models\Company\Attachment;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index()
    {
        $admin = $this->createAdminUser();
        CompanyAdmin::factory(5)->create();
        $response = $this->actingAs($admin)->getJson(route('users.index'));

        $response->assertOk();
        $this->assertCount(6, $response->json('data'));
    }

    #[Test]
    public function single()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $response = $this->actingAs($admin)->getJson(route('users.get-single', ['id' => $admin->id]));

        $response->assertOk();
        $this->assertEquals($admin->id, $response->json('data.id'));
        $this->assertEquals($admin->first_name, $response->json('data.first_name'));
        $this->assertEquals($admin->last_name, $response->json('data.last_name'));
        $this->assertEquals($admin->username, $response->json('data.username'));
    }

    #[Test]
    public function create()
    {
        $role = Role::factory()->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('users.create'), [
            'first_name' => 'Test First Name',
            'last_name' => 'Test Last Name',
            'username' => 'test_username',
            'role_id' => $role->id,
            'status' => UserStatusEnum::ACTIVE->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('users', [
            'first_name' => 'Test First Name',
            'last_name' => 'Test Last Name',
            'username' => 'test_username',
            'status' => UserStatusEnum::ACTIVE->value,
        ]);
        $this->assertEquals('Test First Name', $response->json('data.first_name'));
        $this->assertEquals('Test Last Name', $response->json('data.last_name'));
        $this->assertEquals('test_username', $response->json('data.username'));
    }

    #[Test]
    public function update()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $response = $this->actingAs($admin)->putJson(route('users.update', ['id' => $admin->id]), [
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
        ]);

        $response->assertOk();
        $this->assertEquals('Updated First Name', $admin->fresh()->first_name);
        $this->assertEquals('Updated Last Name', $admin->fresh()->last_name);
    }

    #[Test]
    public function changePassword()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $newPassword = 'password';
        $response = $this->actingAs($admin)->postJson(route('users.change-password', ['id' => $admin->id]), [
            'old_password' => 'password',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        $admin->refresh();
        $response->assertOk();
        $this->assertTrue(Hash::check($newPassword, $admin->password));
    }

    #[Test]
    public function changeTheme()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $response = $this->actingAs($admin)->postJson(route('users.change-theme', ['id' => $admin->id]), [
            'layout_name' => 'fixed',
        ]);

        $response->assertOk();
        $this->assertEquals('fixed', $admin->fresh()->template_theme->layout_name);
    }

    #[Test]
    public function changeAvatar()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $response = $this->actingAs($admin)->postJson(route('users.change-avatar', ['id' => $admin->id]), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $admin->refresh();
        $response->assertOk();
        $this->assertFileExists(storage_path('app/public/' . Attachment::UPLOADS_PATH . '/' . AttachmentTypeEnum::AVATAR->getStoragePath() . '/' . $admin->avatar->path));
    }

    #[Test]
    public function remove()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $response = $this->actingAs($admin)->deleteJson(route('users.delete'), [
            'ids' => [$admin->id],
        ]);

        $response->assertOk();
        $this->assertSoftDeleted($admin);
    }

    #[Test]
    public function devices()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $admin->createTokenWithData('test');

        $response = $this->actingAs($admin)->getJson(route('users.devices', ['id' => $admin->id]));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals($admin->tokens()->first()->id, $response->json('data.0.id'));
        $this->assertCount(1, $response->json('data'));
    }

    #[Test]
    public function revokeDevice()
    {
        $admin = $this->createAdminUser();
        $admin = CompanyAdmin::factory()->create();
        $admin->createTokenWithData('test');
        $token = $admin->tokens()->first();

        $response = $this->actingAs($admin)->deleteJson(route('users.revoke-device', [
            'ids' => [$token->id],
        ]));

        $response->assertOk();
        $this->assertEmpty($admin->tokens()->first());
    }
}
