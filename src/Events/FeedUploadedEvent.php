<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Feed Uploaded Event
 *
 * Fired when a feed is successfully uploaded to SFTP.
 */
class FeedUploadedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $filename,
        public string $remotePath,
        public int $size,
    ) {}
}
