<?php

namespace App\Rules;

use App\Models\Company\CompanyAdmin;
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
        /** @var CompanyAdmin $admin */
        $admin = auth()->user();
        if (! Hash::check($value, $admin->password)) {
            $fail('The old password is incorrect.');
        }
    }
}
