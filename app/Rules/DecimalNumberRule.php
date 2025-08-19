<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DecimalNumberRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_numeric($value) && $this->isValidFormat($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid decimal number with up to 10 digits before and 2 digits after the decimal (negative numbers allowed).';
    }

    /**
     * Validate the format.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isValidFormat($value): bool
    {
        // Allow optional minus sign, up to 10 digits before decimal, and up to 2 after
        return preg_match('/^-?\d{1,10}(\.\d{1,2})?$/', $value) === 1;
    }
}
