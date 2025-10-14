<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Feed Upload Failed Event
 *
 * Fired when a feed upload to SFTP fails.
 */
class FeedUploadFailedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $filename,
        public string $error,
    ) {}
}
