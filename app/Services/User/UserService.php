<?php

namespace App\Services\User;

use App\Enums\Company\AttachmentTypeEnum;
use App\Http\Requests\User\UserCreateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(UserCreateRequest $request): User
    {
        $attributes = $request->validated();
        $attributes['password'] = bcrypt($attributes['password']);

        return User::create($attributes);
    }

    public function updateDetails(User $user, array $request): User
    {
        $user->updateOrFail($request);

        return $user;
    }

    public function updatePassword(User $user, string $newPassword): User
    {
        $user->updateOrFail([
            'password' => Hash::make($newPassword),
        ]);

        return $user;
    }

    public function updateTheme(User $user, array $request): User
    {
        $user->updateOrFail([
            'template_theme' => $request,
        ]);
        $user->refresh();

        return $user;
    }

    public function updateAvatar(User $user, UploadedFile $file): User
    {
        Attachment::upload($file, AttachmentTypeEnum::AVATAR, $user);
        $user->refresh();

        return $user;
    }
}
