<?php

namespace App\Rules;

use App\Models\Company\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckCurrentPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var User $user */
        $user = auth()->user();
        if (! Hash::check($value, $user->password)) {
            $fail('The old password is incorrect.');
        }
    }
}
