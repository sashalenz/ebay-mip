<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Builders;

use Illuminate\Support\Collection;
use Sashalenz\EbayMip\Data\Availability\AvailabilityData;
use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Generators\AvailabilityFeedGenerator;

/**
 * Availability Feed Builder
 *
 * Fluent interface for building availability feeds.
 */
class AvailabilityFeedBuilder
{
    protected AvailabilityFeedGenerator $generator;

    protected array $items = [];

    public function __construct()
    {
        $this->generator = new AvailabilityFeedGenerator;
    }

    public static function make(): static
    {
        return new static;
    }

    public function format(FeedFormat $format): static
    {
        $this->generator = new AvailabilityFeedGenerator($format);
        $this->generator->setData($this->items);

        return $this;
    }

    public function addAvailability(AvailabilityData $data): static
    {
        $this->items[] = $data;
        $this->generator->setData($this->items);

        return $this;
    }

    public function addAvailabilities(Collection|array $items): static
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
