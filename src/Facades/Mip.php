<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Facades;

use Illuminate\Support\Facades\Facade;
use Sashalenz\EbayMip\Builders\AvailabilityFeedBuilder;
use Sashalenz\EbayMip\Builders\FulfillmentFeedBuilder;
use Sashalenz\EbayMip\Builders\ProductFeedBuilder;
use Sashalenz\EbayMip\Upload\SftpUploader;
use Sashalenz\EbayMip\Upload\UploadManager;

/**
 * MIP Facade
 *
 * @method static ProductFeedBuilder product()
 * @method static AvailabilityFeedBuilder availability()
 * @method static FulfillmentFeedBuilder fulfillment()
 * @method static SftpUploader uploader()
 * @method static UploadManager uploadManager()
 *
 * @see \Sashalenz\EbayMip\MipManager
 */
class Mip extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ebay-mip';
    }
}
