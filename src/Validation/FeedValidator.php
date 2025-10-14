<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Validation;

use Sashalenz\EbayMip\Exceptions\ValidationException;

/**
 * Feed Validator
 *
 * Base validator for MIP feeds with eBay-specific rules.
 */
class FeedValidator
{
    protected array $rules = [];

    protected bool $strict;

    protected bool $throwOnError;

    public function __construct()
    {
        $this->strict = config('ebay-mip.validation.strict', true);
        $this->throwOnError = config('ebay-mip.validation.throw', true);
    }

    /**
     * Validate feed data
     *
     * @return array Validation errors (empty if valid)
     *
     * @throws ValidationException
     */
    public function validate(array $data): array
    {
        $errors = [];

        foreach ($data as $index => $record) {
            $recordErrors = $this->validateRecord($record, $index);

            if (! empty($recordErrors)) {
                $errors[$index] = $recordErrors;
            }
        }

        if ($this->throwOnError && ! empty($errors)) {
            throw new ValidationException('Feed validation failed', $errors);
        }

        return $errors;
    }

    /**
     * Validate single record
     */
    protected function validateRecord(mixed $record, int $index): array
    {
        $errors = [];

        foreach ($this->rules as $field => $rule) {
            $value = is_array($record) ? ($record[$field] ?? null) : ($record->$field ?? null);

            $ruleErrors = $rule->validate($value);

            if (! empty($ruleErrors)) {
                $errors[$field] = $ruleErrors;
            }
        }

        return $errors;
    }

    /**
     * Add validation rule
     */
    public function addRule(string $field, callable|object $rule): static
    {
        $this->rules[$field] = $rule;

        return $this;
    }

    /**
     * Set validation rules
     */
    public function setRules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }
}
