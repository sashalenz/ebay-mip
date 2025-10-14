<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Generators;

use League\Csv\Writer;
use Sashalenz\EbayMip\Data\Fulfillment\FulfillmentData;
use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Enums\FeedType;
use Sashalenz\EbayMip\Validation\FeedValidator;

/**
 * Fulfillment Feed Generator
 *
 * Generates fulfillment feeds with shipping/tracking information.
 */
class FulfillmentFeedGenerator extends FeedGenerator
{
    public function __construct(FeedFormat $format = FeedFormat::CSV)
    {
        $this->feedType = FeedType::FULFILLMENT;
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
        $csv->insertOne(['OrderID', 'TrackingNumber', 'Carrier', 'ShipDate', 'ShippingService']);

        foreach ($this->data as $item) {
            /** @var FulfillmentData $item */
            $csv->insertOne([
                $item->orderId,
                $item->trackingNumber,
                $item->carrier,
                $item->shipDate,
                $item->shippingService ?? '',
            ]);
        }

        return $csv->toString();
    }

    protected function generateXml(): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><FulfillmentFeed></FulfillmentFeed>');

        foreach ($this->data as $item) {
            /** @var FulfillmentData $item */
            $node = $xml->addChild('Fulfillment');
            $node->addChild('OrderID', htmlspecialchars($item->orderId));
            $node->addChild('TrackingNumber', htmlspecialchars($item->trackingNumber));
            $node->addChild('Carrier', htmlspecialchars($item->carrier));
            $node->addChild('ShipDate', $item->shipDate);

            if ($item->shippingService) {
                $node->addChild('ShippingService', htmlspecialchars($item->shippingService));
            }
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
    }
}
