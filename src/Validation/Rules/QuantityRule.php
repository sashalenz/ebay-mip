<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation\Rules;

/**
 * Quantity Validation Rule
 *
 * Validates quantity against eBay MIP requirements.
 */
class QuantityRule
{
    public function validate(mixed $value): array
    {
        $errors = [];

        if ($value === null || $value === '') {
            $errors[] = 'Quantity is required';

            return $errors;
        }

        if (! is_numeric($value)) {
            $errors[] = 'Quantity must be a number';

            return $errors;
        }

        $quantity = (int) $value;

        if ($quantity < 0) {
            $errors[] = 'Quantity cannot be negative';
        }

        if ($quantity > 999999) {
            $errors[] = 'Quantity seems unusually high (max 999,999)';
        }

        return $errors;
    }
}
