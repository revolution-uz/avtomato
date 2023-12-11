<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^998(20|33|88|90|91|93|94|95|97|98|99)\d{7}$/', $value)) {
            $fail('The ' . $attribute . ' is invalid phone number.');
        }
    }
}
