<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Enums;

/**
 * Feed Type
 *
 * eBay MIP supported feed types.
 */
enum FeedType: string
{
    case PRODUCT = 'product';
    case DISTRIBUTION = 'distribution';
    case AVAILABILITY = 'availability';
    case ORDER_REPORT = 'order_report';
    case FULFILLMENT = 'fulfillment';
    case COMBINED = 'combined';

    /**
     * Get feed type label
     */
    public function label(): string
    {
        return match ($this) {
            self::PRODUCT => 'Product Feed',
            self::DISTRIBUTION => 'Distribution Feed',
            self::AVAILABILITY => 'Availability Feed',
            self::ORDER_REPORT => 'Order Report Feed',
            self::FULFILLMENT => 'Fulfillment Feed',
            self::COMBINED => 'Combined Feed',
        };
    }

    /**
     * Check if feed type supports uploads
     */
    public function supportsUpload(): bool
    {
        return match ($this) {
            self::PRODUCT, self::DISTRIBUTION, self::AVAILABILITY, self::FULFILLMENT, self::COMBINED => true,
            self::ORDER_REPORT => false, // Download only
        };
    }
}
