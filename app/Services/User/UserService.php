<?php

namespace App\Services\User;

use App\Enums\Company\AttachmentTypeEnum;
use App\Http\Requests\User\AvatarRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(UserCreateRequest $request): User
    {
        $attributes = $request->validated();
        $attributes['password'] = bcrypt($attributes['password']);

        return User::create($attributes);
    }

    public function updateDetails(User $user, UserUpdateRequest $request): User
    {
        $attributes = $request->validated();
        $user->updateOrFail($attributes);

        return $user;
    }

    public function updatePassword(User $user, PasswordRequest $request): User
    {
        $user->updateOrFail([
            'password' => Hash::make($request->password),
        ]);

        return $user;
    }

    public function updateTheme(User $user, UserThemeRequest $request): User
    {
        $user->updateOrFail([
            'template_theme' => $request->getData(),
        ]);

        return $user;
    }

    public function updateAvatar(User $user, AvatarRequest $request): User
    {
        Attachment::upload($request->file('avatar'), AttachmentTypeEnum::AVATAR, $user);
        $user->refresh();

        return $user;
    }
}
