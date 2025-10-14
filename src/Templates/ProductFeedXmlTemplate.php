<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Templates;

use Sashalenz\EbayMip\Data\Product\ProductData;

/**
 * Product Feed XML Template
 *
 * Generates XML format product feeds for eBay MIP.
 */
class ProductFeedXmlTemplate
{
    /**
     * Generate XML content from product data
     */
    public static function generate(array $products): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Products></Products>');

        foreach ($products as $product) {
            self::addProduct($xml, $product);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
    }

    /**
     * Add product to XML
     */
    protected static function addProduct(\SimpleXMLElement $xml, ProductData $product): void
    {
        $productNode = $xml->addChild('Product');

        $productNode->addChild('SKU', htmlspecialchars($product->sku));
        $productNode->addChild('Title', htmlspecialchars($product->title));

        if ($product->description) {
            $productNode->addChild('Description', htmlspecialchars($product->description));
        }

        $productNode->addChild('Price', $product->price);
        $productNode->addChild('Quantity', (string) $product->quantity);
        $productNode->addChild('CategoryID', $product->categoryId);
        $productNode->addChild('Condition', $product->condition ?? 'NEW');

        if ($product->brand) {
            $productNode->addChild('Brand', htmlspecialchars($product->brand));
        }

        if ($product->mpn) {
            $productNode->addChild('MPN', htmlspecialchars($product->mpn));
        }

        // Images
        if (! empty($product->images)) {
            $imagesNode = $productNode->addChild('Images');
            foreach ($product->images as $imageUrl) {
                $imagesNode->addChild('Image', htmlspecialchars($imageUrl));
            }
        }

        // Item Specifics
        if (! empty($product->itemSpecifics)) {
            $specificsNode = $productNode->addChild('ItemSpecifics');
            foreach ($product->itemSpecifics as $name => $value) {
                $specificNode = $specificsNode->addChild('NameValueList');
                $specificNode->addChild('Name', htmlspecialchars($name));
                $specificNode->addChild('Value', htmlspecialchars($value));
            }
        }
    }
}
