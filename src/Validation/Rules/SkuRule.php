<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation\Rules;

/**
 * SKU Validation Rule
 *
 * Validates SKU against eBay MIP requirements.
 */
class SkuRule
{
    public function validate(?string $value): array
    {
        $errors = [];

        if (empty($value)) {
            $errors[] = 'SKU is required';

            return $errors;
        }

        // Max 50 characters
        if (mb_strlen($value) > 50) {
            $errors[] = 'SKU must not exceed 50 characters';
        }

        // Alphanumeric, hyphens, underscores only
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
            $errors[] = 'SKU can only contain letters, numbers, hyphens, and underscores';
        }

        return $errors;
    }
}
