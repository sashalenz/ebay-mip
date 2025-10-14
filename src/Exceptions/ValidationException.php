<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Exceptions;

use Exception;

/**
 * Validation Exception
 *
 * Thrown when feed validation fails.
 */
class ValidationException extends Exception
{
    protected array $errors;

    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
