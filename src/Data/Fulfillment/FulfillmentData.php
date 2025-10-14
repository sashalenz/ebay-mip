<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Data\Fulfillment;

use Spatie\LaravelData\Data;

/**
 * Fulfillment Data
 *
 * Contains order fulfillment (shipping/tracking) data.
 */
class FulfillmentData extends Data
{
    public function __construct(
        public string $orderId,
        public string $trackingNumber,
        public string $carrier,
        public string $shipDate,
        public ?string $shippingService = null,
    ) {}
}
