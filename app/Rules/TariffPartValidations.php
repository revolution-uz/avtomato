<?php

namespace App\Rules;

use App\Models\Company\Tariff;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TariffPartValidations implements DataAwareRule, ValidationRule
{
    /**
     * All the data under validation.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tariff = Tariff::findOrFail($this->data['tariff_id'] ?? $this->data['installment']['tariff_id']);
        /** @var Tariff|null $tariff */
        if ($tariff == null) {
            return;
        }
        if ($attribute === 'period' && ($value < $tariff->min_period || $value > $tariff->max_period)) {
            $fail("The period must be between {$tariff->min_period} and {$tariff->max_period}.");
        }

        if ($attribute === 'amount' && ($value < $tariff->min_amount || $value > $tariff->max_amount)) {
            $fail("The amount must be between {$tariff->min_amount} and {$tariff->max_amount}.");
        }

        if ($attribute === 'initial_fee' && ($value < $tariff->min_initial_fee || $value > $tariff->max_initial_fee)) {
            $fail("The initial_fee must be between {$tariff->min_initial_fee} and {$tariff->max_initial_fee}.");
        }
    }

    /**
     * Set the data under validation.
     *
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
