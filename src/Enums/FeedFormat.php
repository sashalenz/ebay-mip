<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Enums;

/**
 * Feed Format
 *
 * Supported feed file formats.
 */
enum FeedFormat: string
{
    case CSV = 'csv';
    case XML = 'xml';

    /**
     * Get file extension
     */
    public function extension(): string
    {
        return $this->value;
    }

    /**
     * Get MIME type
     */
    public function mimeType(): string
    {
        return match ($this) {
            self::CSV => 'text/csv',
            self::XML => 'application/xml',
        };
    }
}
