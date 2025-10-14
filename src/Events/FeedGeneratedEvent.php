<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Feed Generated Event
 *
 * Fired when a MIP feed is generated.
 */
class FeedGeneratedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $feedType,
        public int $recordCount,
        public string $format,
    ) {}
}
