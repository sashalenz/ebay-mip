<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Generators;

use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Enums\FeedType;
use Sashalenz\EbayMip\Templates\ProductFeedTemplate;
use Sashalenz\EbayMip\Templates\ProductFeedXmlTemplate;
use Sashalenz\EbayMip\Validation\FeedValidator;
use Sashalenz\EbayMip\Validation\Rules\CategoryRule;
use Sashalenz\EbayMip\Validation\Rules\ImageRule;
use Sashalenz\EbayMip\Validation\Rules\PriceRule;
use Sashalenz\EbayMip\Validation\Rules\QuantityRule;
use Sashalenz\EbayMip\Validation\Rules\SkuRule;
use Sashalenz\EbayMip\Validation\Rules\TitleRule;

/**
 * Product Feed Generator
 *
 * Generates product feeds in CSV or XML format for eBay MIP.
 */
class ProductFeedGenerator extends FeedGenerator
{
    public function __construct(FeedFormat $format = FeedFormat::CSV)
    {
        $this->feedType = FeedType::PRODUCT;
        $this->format = $format;
        $this->validator = $this->createValidator();
    }

    /**
     * Generate feed content
     */
    public function generate(): string
    {
        // Validate if strict mode
        if (config('ebay-mip.validation.strict', true)) {
            $errors = $this->validate();
            if (! empty($errors) && config('ebay-mip.validation.throw', true)) {
                throw new \Sashalenz\EbayMip\Exceptions\ValidationException(
                    'Product feed validation failed',
                    $errors
                );
            }
        }

        // Generate based on format
        $this->generatedContent = match ($this->format) {
            FeedFormat::CSV => ProductFeedTemplate::generate($this->data),
            FeedFormat::XML => ProductFeedXmlTemplate::generate($this->data),
        };

        return $this->generatedContent;
    }

    /**
     * Create validator with product-specific rules
     */
    protected function createValidator(): FeedValidator
    {
        $validator = new FeedValidator;

        $validator->setRules([
            'sku' => new SkuRule,
            'title' => new TitleRule,
            'price' => new PriceRule,
            'quantity' => new QuantityRule,
            'categoryId' => new CategoryRule,
            'images' => new ImageRule,
        ]);

        return $validator;
    }
}
