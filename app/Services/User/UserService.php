<?php

namespace App\Services\User;

use App\Http\Requests\User\AvatarRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\Company\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
            'password' => Hash::make('password'),
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
        $photo = $request->file('avatar');
        $file_name = uniqid() . '.' . $photo->extension();
        $photo->storeAs(User::AVATAR_PATH, $file_name, ['disk' => 'public']);

        $thumbnail = Image::make($photo)->resize(300, 300);
        $previewPath = User::THUMBNAIL_PATH . '/' . $file_name;
        Storage::disk('public')->put($previewPath, $thumbnail->encode());
        $user->updateOrFail(['avatar' => $file_name]);

        return $user;
    }
}
