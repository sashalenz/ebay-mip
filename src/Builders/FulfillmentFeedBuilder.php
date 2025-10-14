<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Builders;

use Illuminate\Support\Collection;
use Sashalenz\EbayMip\Data\Fulfillment\FulfillmentData;
use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Generators\FulfillmentFeedGenerator;

/**
 * Fulfillment Feed Builder
 *
 * Fluent interface for building fulfillment feeds.
 */
class FulfillmentFeedBuilder
{
    protected FulfillmentFeedGenerator $generator;

    protected array $items = [];

    public function __construct()
    {
        $this->generator = new FulfillmentFeedGenerator;
    }

    public static function make(): static
    {
        return new static;
    }

    public function format(FeedFormat $format): static
    {
        $this->generator = new FulfillmentFeedGenerator($format);
        $this->generator->setData($this->items);

        return $this;
    }

    public function addFulfillment(FulfillmentData $data): static
    {
        $this->items[] = $data;
        $this->generator->setData($this->items);

        return $this;
    }

    public function addFulfillments(Collection|array $items): static
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }

        $this->generator->setData($this->items);

        return $this;
    }

    public function save(?string $path = null): string
    {
        return $this->generator->save($path);
    }

    public function getContent(): string
    {
        return $this->generator->getContent();
    }
}
