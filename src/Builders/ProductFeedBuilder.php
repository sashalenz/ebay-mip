<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Builders;

use Illuminate\Support\Collection;
use Sashalenz\EbayMip\Data\Product\ProductData;
use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Events\FeedGeneratedEvent;
use Sashalenz\EbayMip\Generators\ProductFeedGenerator;

/**
 * Product Feed Builder
 *
 * Fluent interface for building product feeds.
 */
class ProductFeedBuilder
{
    protected ProductFeedGenerator $generator;

    protected array $products = [];

    public function __construct()
    {
        $this->generator = new ProductFeedGenerator;
    }

    /**
     * Create new builder instance
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * Set feed format
     */
    public function format(FeedFormat $format): static
    {
        $this->generator = new ProductFeedGenerator($format);
        $this->generator->setData($this->products);

        return $this;
    }

    /**
     * Add single product
     */
    public function addProduct(ProductData $product): static
    {
        $this->products[] = $product;
        $this->generator->setData($this->products);

        return $this;
    }

    /**
     * Add multiple products
     */
    public function addProducts(Collection|array $products): static
    {
        foreach ($products as $product) {
            $this->products[] = $product;
        }

        $this->generator->setData($this->products);

        return $this;
    }

    /**
     * Validate feed data
     */
    public function validate(): static
    {
        $this->generator->validate();

        return $this;
    }

    /**
     * Generate feed content
     */
    public function generate(): static
    {
        $this->generator->generate();

        event(new FeedGeneratedEvent(
            feedType: 'product',
            recordCount: count($this->products),
            format: $this->generator->format->value
        ));

        return $this;
    }

    /**
     * Save feed to disk
     */
    public function save(?string $path = null): string
    {
        $filename = $this->generator->save($path);

        return $filename;
    }

    /**
     * Get feed content
     */
    public function getContent(): string
    {
        return $this->generator->getContent();
    }

    /**
     * Upload feed to SFTP (requires SftpUploader)
     */
    public function upload(): bool
    {
        $filename = $this->save();
        $path = config('ebay-mip.feeds.storage_path').'/'.$filename;

        $uploader = app(\Sashalenz\EbayMip\Upload\SftpUploader::class);

        return $uploader->upload($path, '/inbound/'.$filename);
    }
}
