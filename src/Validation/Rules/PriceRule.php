<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation\Rules;

/**
 * Price Validation Rule
 *
 * Validates price against eBay MIP requirements.
 */
class PriceRule
{
    public function validate(mixed $value): array
    {
        $errors = [];

        if (empty($value)) {
            $errors[] = 'Price is required';

            return $errors;
        }

        // Must be numeric
        if (! is_numeric($value)) {
            $errors[] = 'Price must be a valid number';

            return $errors;
        }

        $price = (float) $value;

        // Must be positive
        if ($price <= 0) {
            $errors[] = 'Price must be greater than zero';
        }

        // Max 2 decimal places
        if (round($price, 2) != $price) {
            $errors[] = 'Price must have maximum 2 decimal places';
        }

        // Reasonable max (prevent typos)
        if ($price > 999999.99) {
            $errors[] = 'Price seems unusually high (max 999,999.99)';
        }

        return $errors;
    }
}
