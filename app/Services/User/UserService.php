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

    public function updateDetails(User $user, UserUpdateRequest $request): bool
    {
        $attributes = $request->validated();

        return $user->update($attributes);
    }

    public function updatePassword(User $user, PasswordRequest $request): bool
    {
        return $user->update([
            'password' => Hash::make('password'),
        ]);
    }

    public function updateTheme(User $user, UserThemeRequest $request): bool
    {
        return $user->update([
            'template_theme' => $request->getData(),
        ]);
    }

    public function updateAvatar(User $user, AvatarRequest $request)
    {
        $photo = $request->file('avatar');
        $file_name = uniqid() . '.' . $photo->extension();
        $photo->storeAs(User::AVATAR_PATH, $file_name, ['disk' => 'public']);

        $thumbnail = Image::make($photo)->resize(300, 300);
        $previewPath = User::THUMBNAIL_PATH . '/' . $file_name;
        Storage::disk('public')->put($previewPath, $thumbnail->encode());

        return $user->update(['avatar' => $file_name]);
    }
}
