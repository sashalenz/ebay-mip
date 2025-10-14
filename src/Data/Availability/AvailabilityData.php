<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Data\Availability;

use Spatie\LaravelData\Data;

/**
 * Availability Data
 *
 * Contains inventory availability data.
 */
class AvailabilityData extends Data
{
    public function __construct(
        public string $sku,
        public int $quantity,
        public ?string $availabilityType = 'IN_STOCK',
    ) {}
}
