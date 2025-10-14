<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Generators;

use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Enums\FeedType;
use Sashalenz\EbayMip\Validation\FeedValidator;

/**
 * Combined Feed Generator
 *
 * Generates combined feeds with Product + Distribution + Availability data.
 * Most efficient for full inventory synchronization.
 */
class CombinedFeedGenerator extends FeedGenerator
{
    protected array $products = [];

    protected array $distributions = [];

    protected array $availabilities = [];

    public function __construct(FeedFormat $format = FeedFormat::CSV)
    {
        $this->feedType = FeedType::COMBINED;
        $this->format = $format;
        $this->validator = new FeedValidator;
    }

    public function setProducts(array $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function setDistributions(array $distributions): static
    {
        $this->distributions = $distributions;

        return $this;
    }

    public function setAvailabilities(array $availabilities): static
    {
        $this->availabilities = $availabilities;

        return $this;
    }

    public function generate(): string
    {
        // Combined feed typically uses all three generators
        $productGen = new ProductFeedGenerator($this->format);
        $productGen->setData($this->products);

        $this->generatedContent = $productGen->generate();

        // In real implementation, would merge all three feed types
        // For now, just using product data as example

        return $this->generatedContent;
    }
}
