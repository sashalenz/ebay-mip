<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip;

use Sashalenz\EbayMip\Builders\AvailabilityFeedBuilder;
use Sashalenz\EbayMip\Builders\FulfillmentFeedBuilder;
use Sashalenz\EbayMip\Builders\ProductFeedBuilder;
use Sashalenz\EbayMip\Upload\SftpUploader;
use Sashalenz\EbayMip\Upload\UploadManager;

/**
 * MIP Manager
 *
 * Central manager for MIP operations.
 */
class MipManager
{
    /**
     * Create product feed builder
     */
    public function product(): ProductFeedBuilder
    {
        return ProductFeedBuilder::make();
    }

    /**
     * Create availability feed builder
     */
    public function availability(): AvailabilityFeedBuilder
    {
        return AvailabilityFeedBuilder::make();
    }

    /**
     * Create fulfillment feed builder
     */
    public function fulfillment(): FulfillmentFeedBuilder
    {
        return FulfillmentFeedBuilder::make();
    }

    /**
     * Get SFTP uploader instance
     */
    public function uploader(): SftpUploader
    {
        return app(SftpUploader::class);
    }

    /**
     * Get upload manager instance
     */
    public function uploadManager(): UploadManager
    {
        return app(UploadManager::class);
    }
}
