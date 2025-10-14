<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Data\Distribution;

use Spatie\LaravelData\Data;

/**
 * Distribution Data
 *
 * Contains distribution (multi-location inventory) data.
 */
class DistributionData extends Data
{
    public function __construct(
        public string $sku,
        public string $locationId,
        public int $quantity,
    ) {}
}
