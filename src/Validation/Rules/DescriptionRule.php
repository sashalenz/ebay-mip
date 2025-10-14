<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation\Rules;

/**
 * Description Validation Rule
 *
 * Validates product description against eBay MIP requirements.
 */
class DescriptionRule
{
    public function validate(?string $value): array
    {
        $errors = [];

        if (empty($value)) {
            return $errors; // Description is optional
        }

        // Max 500,000 characters
        if (mb_strlen($value) > 500000) {
            $errors[] = 'Description must not exceed 500,000 characters';
        }

        return $errors;
    }
}
