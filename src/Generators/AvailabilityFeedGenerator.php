<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Generators;

use League\Csv\Writer;
use Sashalenz\EbayMip\Data\Availability\AvailabilityData;
use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Enums\FeedType;
use Sashalenz\EbayMip\Validation\FeedValidator;

/**
 * Availability Feed Generator
 *
 * Generates availability feeds for inventory quantity updates.
 */
class AvailabilityFeedGenerator extends FeedGenerator
{
    public function __construct(FeedFormat $format = FeedFormat::CSV)
    {
        $this->feedType = FeedType::AVAILABILITY;
        $this->format = $format;
        $this->validator = new FeedValidator;
    }

    public function generate(): string
    {
        $this->generatedContent = match ($this->format) {
            FeedFormat::CSV => $this->generateCsv(),
            FeedFormat::XML => $this->generateXml(),
        };

        return $this->generatedContent;
    }

    protected function generateCsv(): string
    {
        $csv = Writer::createFromString();
        $csv->insertOne(['SKU', 'Quantity', 'AvailabilityType']);

        foreach ($this->data as $item) {
            /** @var AvailabilityData $item */
            $csv->insertOne([
                $item->sku,
                $item->quantity,
                $item->availabilityType ?? 'IN_STOCK',
            ]);
        }

        return $csv->toString();
    }

    protected function generateXml(): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><AvailabilityFeed></AvailabilityFeed>');

        foreach ($this->data as $item) {
            /** @var AvailabilityData $item */
            $node = $xml->addChild('Availability');
            $node->addChild('SKU', htmlspecialchars($item->sku));
            $node->addChild('Quantity', (string) $item->quantity);
            $node->addChild('AvailabilityType', $item->availabilityType ?? 'IN_STOCK');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
    }
}
