<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\User;

use App\Enums\Company\UserStatusEnum;
use App\Models\Company\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $admin = $this->createAdminUser();
        User::factory(5)->create();
        $response = $this->actingAs($admin)->getJson(route('users.index'));

        $response->assertOk();
        $this->assertCount(6, $response->json('data'));
    }

    /**
     * @test
     */
    public function single()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->getJson(route('users.get-single', ['id' => $user->id]));

        $response->assertOk();
        $this->assertEquals($user->id, $response->json('data.id'));
        $this->assertEquals($user->first_name, $response->json('data.first_name'));
        $this->assertEquals($user->last_name, $response->json('data.last_name'));
        $this->assertEquals($user->username, $response->json('data.username'));
    }

    /**
     * @test
     */
    public function create()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('users.create'), [
            'first_name' => 'Test First Name',
            'last_name' => 'Test Last Name',
            'username' => 'test_username',
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

    /**
     * @test
     */
    public function update()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->putJson(route('users.update', ['id' => $user->id]), [
            'first_name' => 'Updated First Name',
            'last_name' => 'Updated Last Name',
        ]);

        $response->assertOk();
        $this->assertEquals('Updated First Name', $user->fresh()->first_name);
        $this->assertEquals('Updated Last Name', $user->fresh()->last_name);
    }

    /**
     * @test
     */
    public function changePassword()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $newPassword = 'password';
        $response = $this->actingAs($admin)->postJson(route('users.change-password', ['id' => $user->id]), [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        $user->refresh();
        $response->assertOk();
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    /**
     * @test
     */
    public function changeTheme()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->postJson(route('users.change-theme', ['id' => $user->id]), [
            'layout_name' => 'fixed',
        ]);

        $response->assertOk();
        $this->assertEquals('fixed', $user->fresh()->template_theme->layout_name);
    }

    /**
     * @test
     */
    public function changeAvatar()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->postJson(route('users.change-avatar', ['id' => $user->id]), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk();
        $file_name = $user->fresh()->avatar;
        $this->assertFileExists(storage_path('app/public/' . User::AVATAR_PATH . '/' . $file_name));
        $this->assertFileExists(storage_path('app/public/' . User::THUMBNAIL_PATH . '/' . $file_name));
    }

    /**
     * @test
     */
    public function remove()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->deleteJson(route('users.delete'), [
            'ids' => [$user->id],
        ]);

        $response->assertOk();
        $this->assertSoftDeleted($user);
    }

    /**
     * @test
     */
    public function devices()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $user->createTokenWithData('test');

        $response = $this->actingAs($admin)->getJson(route('users.devices', ['id' => $user->id]));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals($user->tokens()->first()->id, $response->json('data.0.id'));
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * @test
     */
    public function revokeDevice()
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->create();
        $user->createTokenWithData('test');
        $token = $user->tokens()->first();

        $response = $this->actingAs($admin)->deleteJson(route('users.revoke-device', [
            'ids' => [$token->id],
        ]));

        $response->assertOk();
        $this->assertEmpty($user->tokens()->first());
    }
}
