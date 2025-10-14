<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Data\Product;

use Spatie\LaravelData\Data;

/**
 * Product Data
 *
 * Contains product feed data for MIP.
 */
class ProductData extends Data
{
    public function __construct(
        public string $sku,
        public string $title,
        public string $price,
        public int $quantity,
        public string $categoryId,
        public ?string $description = null,
        public ?array $images = null,
        public ?array $itemSpecifics = null,
        public ?array $variations = null,
        public ?string $condition = 'NEW',
        public ?string $brand = null,
        public ?string $mpn = null,
        public ?string $ean = null,
        public ?string $upc = null,
        public ?string $isbn = null,
        public ?float $shippingCost = null,
        public ?string $shippingService = null,
        public ?int $dispatchTimeMax = null,
        public ?string $returnPolicy = null,
        public ?string $paymentPolicy = null,
        public ?string $fulfillmentPolicy = null,
    ) {}
}
