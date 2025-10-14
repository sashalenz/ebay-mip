<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation\Rules;

/**
 * Category Validation Rule
 *
 * Validates eBay category ID.
 */
class CategoryRule
{
    public function validate(?string $value): array
    {
        $errors = [];

        if (empty($value)) {
            $errors[] = 'Category ID is required';

            return $errors;
        }

        // Must be numeric
        if (! is_numeric($value)) {
            $errors[] = 'Category ID must be numeric';

            return $errors;
        }

        // Must be positive
        if ((int) $value <= 0) {
            $errors[] = 'Category ID must be a positive number';
        }

        return $errors;
    }
}
