<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Templates;

use League\Csv\Writer;
use Sashalenz\EbayMip\Data\Product\ProductData;

/**
 * Product Feed CSV Template
 *
 * Generates CSV format product feeds for eBay MIP.
 */
class ProductFeedTemplate
{
    /**
     * Get CSV column headers
     */
    public static function getHeaders(): array
    {
        return [
            'SKU',
            'Title',
            'Description',
            'Price',
            'Quantity',
            'CategoryID',
            'Condition',
            'Brand',
            'MPN',
            'EAN',
            'UPC',
            'ISBN',
            'PictureURL',
            'ShippingCost',
            'ShippingService',
            'DispatchTimeMax',
            'ReturnPolicy',
            'PaymentPolicy',
            'FulfillmentPolicy',
        ];
    }

    /**
     * Convert ProductData to CSV row
     */
    public static function toRow(ProductData $product): array
    {
        return [
            $product->sku,
            $product->title,
            $product->description ?? '',
            $product->price,
            $product->quantity,
            $product->categoryId,
            $product->condition ?? 'NEW',
            $product->brand ?? '',
            $product->mpn ?? '',
            $product->ean ?? '',
            $product->upc ?? '',
            $product->isbn ?? '',
            ! empty($product->images) ? implode('|', $product->images) : '',
            $product->shippingCost ?? '',
            $product->shippingService ?? '',
            $product->dispatchTimeMax ?? '',
            $product->returnPolicy ?? '',
            $product->paymentPolicy ?? '',
            $product->fulfillmentPolicy ?? '',
        ];
    }

    /**
     * Generate CSV content from product data
     */
    public static function generate(array $products): string
    {
        $csv = Writer::createFromString();
        $csv->setDelimiter(',');
        $csv->setEnclosure('"');

        // Add headers
        $csv->insertOne(self::getHeaders());

        // Add data rows
        foreach ($products as $product) {
            $csv->insertOne(self::toRow($product));
        }

        return $csv->toString();
    }
}
