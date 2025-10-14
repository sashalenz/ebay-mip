<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Data\Response;

use Spatie\LaravelData\Data;

/**
 * Feed Response Data
 *
 * Contains parsed MIP response file data.
 */
class FeedResponseData extends Data
{
    public function __construct(
        public int $totalRecords,
        public int $successRecords,
        public int $errorRecords,
        public array $errors, // SKU => error message
        public ?string $feedId = null,
        public ?string $processingStatus = null,
    ) {}
}
