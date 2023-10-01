<?php

namespace App\Services\User;

use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\User;

class UserService
{
    public function create(UserCreateRequest $request): User
    {
        $attributes = $request->validated();
        $attributes['password'] = bcrypt($attributes['password']);

        return User::create($attributes);
    }

    public function updateDetails(User $user, UserUpdateRequest $request): bool
    {
        if ($request->avatar) {
            $avatar = Attachment::whereId($request->avatar)->firstOrFail();
            $user->avatars()->save($avatar);
        }

        $attributes = $request->validated();
        unset($attributes['avatar']);

        return $user->update($attributes);
    }

    public function updatePassword(User $user, PasswordRequest $request): bool
    {
        return $user->update([
            'password' => bcrypt($request->password),
        ]);
    }

    public function updateTheme(User $user, UserThemeRequest $request): bool
    {
        return $user->update([
            'template_theme' => $request->getData(),
        ]);
    }
}
