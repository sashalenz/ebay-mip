<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Generators;

use League\Csv\Writer;
use Sashalenz\EbayMip\Data\Distribution\DistributionData;
use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Enums\FeedType;
use Sashalenz\EbayMip\Validation\FeedValidator;

/**
 * Distribution Feed Generator
 *
 * Generates distribution feeds for multi-location inventory.
 */
class DistributionFeedGenerator extends FeedGenerator
{
    public function __construct(FeedFormat $format = FeedFormat::CSV)
    {
        $this->feedType = FeedType::DISTRIBUTION;
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
        $csv->insertOne(['SKU', 'LocationID', 'Quantity']);

        foreach ($this->data as $item) {
            /** @var DistributionData $item */
            $csv->insertOne([
                $item->sku,
                $item->locationId,
                $item->quantity,
            ]);
        }

        return $csv->toString();
    }

    protected function generateXml(): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><DistributionFeed></DistributionFeed>');

        foreach ($this->data as $item) {
            /** @var DistributionData $item */
            $node = $xml->addChild('Distribution');
            $node->addChild('SKU', htmlspecialchars($item->sku));
            $node->addChild('LocationID', htmlspecialchars($item->locationId));
            $node->addChild('Quantity', (string) $item->quantity);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
    }
}
