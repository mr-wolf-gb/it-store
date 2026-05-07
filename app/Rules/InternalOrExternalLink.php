<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class InternalOrExternalLink implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid internal path or an http/https URL.');

            return;
        }

        $normalizedValue = trim($value);

        if ($normalizedValue === '') {
            $fail('The :attribute must be a valid internal path or an http/https URL.');

            return;
        }

        if (Str::startsWith($normalizedValue, '/')) {
            return;
        }

        if (filter_var($normalizedValue, FILTER_VALIDATE_URL) === false) {
            $fail('The :attribute must be a valid internal path or an http/https URL.');

            return;
        }

        $scheme = strtolower((string) parse_url($normalizedValue, PHP_URL_SCHEME));

        if (! in_array($scheme, ['http', 'https'], true)) {
            $fail('The :attribute must be a valid internal path or an http/https URL.');
        }
    }
}
