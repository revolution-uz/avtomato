<?php

namespace App\Services\User;

use App\DTOs\UserTemplateThemeDto;
use App\Enums\Company\AttachmentTypeEnum;
use App\Http\Requests\User\UserCreateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\CompanyAdmin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(UserCreateRequest $request): CompanyAdmin
    {
        $attributes = $request->validated();
        $attributes['password'] = bcrypt($attributes['password']);

        return CompanyAdmin::create($attributes);
    }

    public function updateDetails(CompanyAdmin $admin, array $request): CompanyAdmin
    {
        $admin->updateOrFail($request);

        return $admin;
    }

    public function updatePassword(CompanyAdmin $admin, string $newPassword): CompanyAdmin
    {
        $admin->updateOrFail([
            'password' => Hash::make($newPassword),
        ]);

        return $admin;
    }

    public function updateTheme(CompanyAdmin $admin, array|UserTemplateThemeDto $request): CompanyAdmin
    {
        $admin->updateOrFail([
            'template_theme' => $request,
        ]);
        $admin->refresh();

        return $admin;
    }

    public function updateAvatar(CompanyAdmin $admin, UploadedFile $file): CompanyAdmin
    {
        Attachment::upload($file, AttachmentTypeEnum::AVATAR, $admin);
        $admin->refresh();

        return $admin;
    }
}
