<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Generators\Contracts;

/**
 * Feed Generator Interface
 *
 * Contract for all MIP feed generators.
 */
interface FeedGeneratorInterface
{
    /**
     * Generate feed content
     *
     * @return string Feed content (CSV or XML)
     */
    public function generate(): string;

    /**
     * Validate feed data
     *
     * @return array Validation errors (empty if valid)
     */
    public function validate(): array;

    /**
     * Save feed to disk
     *
     * @param  string|null  $path  Custom save path
     * @return string Saved filename
     */
    public function save(?string $path = null): string;
}
