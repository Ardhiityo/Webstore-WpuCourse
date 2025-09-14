<?php

namespace App\Rules;

use App\Services\PaymentMethodQueryService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPaymentMethodHash implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $found = app(PaymentMethodQueryService::class)->getPaymentMethodByHash($value);

        if (!$found) {
            $fail('Ups, Payment method is not valid');
        }
    }
}
