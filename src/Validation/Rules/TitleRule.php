<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation\Rules;

/**
 * Title Validation Rule
 *
 * Validates product title against eBay MIP requirements.
 */
class TitleRule
{
    public function validate(?string $value): array
    {
        $errors = [];

        if (empty($value)) {
            $errors[] = 'Title is required';

            return $errors;
        }

        // Max 80 characters
        if (mb_strlen($value) > 80) {
            $errors[] = 'Title must not exceed 80 characters';
        }

        // No HTML tags
        if ($value !== strip_tags($value)) {
            $errors[] = 'Title must not contain HTML tags';
        }

        // No excessive capitalization
        $uppercase = preg_replace('/[^A-Z]/', '', $value);
        if (mb_strlen($uppercase) > mb_strlen($value) * 0.5) {
            $errors[] = 'Title has too many uppercase letters';
        }

        return $errors;
    }
}
