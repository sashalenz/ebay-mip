<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation\Rules;

/**
 * Image Validation Rule
 *
 * Validates product images against eBay MIP requirements.
 */
class ImageRule
{
    public function validate(mixed $value): array
    {
        $errors = [];

        if (empty($value)) {
            return $errors; // Images are optional
        }

        if (! is_array($value)) {
            $errors[] = 'Images must be an array';

            return $errors;
        }

        // Max 12 images
        if (count($value) > 12) {
            $errors[] = 'Maximum 12 images allowed';
        }

        foreach ($value as $index => $url) {
            // Must be valid URL
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                $errors[] = "Image {$index}: Invalid URL format";

                continue;
            }

            // Must be HTTPS
            if (! str_starts_with($url, 'https://')) {
                $errors[] = "Image {$index}: Must use HTTPS protocol";
            }

            // Check file extension
            $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (! in_array($extension, $allowedExtensions)) {
                $errors[] = "Image {$index}: Must be JPG, PNG, or GIF";
            }
        }

        return $errors;
    }
}
