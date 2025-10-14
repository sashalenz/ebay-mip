# eBay MIP Feed Generator for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sashalenz/ebay-mip.svg?style=flat-square)](https://packagist.org/packages/sashalenz/ebay-mip)
[![Total Downloads](https://img.shields.io/packagist/dt/sashalenz/ebay-mip.svg?style=flat-square)](https://packagist.org/packages/sashalenz/ebay-mip)

A modern Laravel package for eBay Merchant Integration Platform (MIP). Generate feed files (CSV/XML), automate SFTP uploads, and manage bulk inventory operations with ease.

## Features

- 🚀 Built for Laravel 12+ and PHP 8.4+
- 📦 **6 Feed Types**: Product, Distribution, Availability, Fulfillment, Order Report, Combined
- 📝 **Dual Format Support**: CSV and XML
- ✅ **Full eBay Validation**: Enforce MIP rules before generation
- 🏗️ **Fluent Builder API**: Clean, readable feed construction
- 📤 **SFTP Automation**: Automatic uploads via Laravel Scheduler
- 🔄 **Retry Logic**: 3 attempts with exponential backoff
- 📊 **Upload Tracking**: Database history of all feed uploads
- 🎪 **Laravel Events**: Listen to feed lifecycle events
- ⚡ **Performance**: Upload 50,000+ SKUs in 30 minutes
- 🔐 **Type-Safe**: Spatie Laravel Data objects

## What is MIP?

The Merchant Integration Platform (MIP) is eBay's feed-based bulk upload system for enterprise sellers. Instead of making thousands of API calls, you upload CSV/XML feed files via SFTP, and eBay processes them efficiently.

**MIP Benefits:**
- Upload 50,000+ SKUs in 30 minutes
- Industry-standard CSV/XML formats
- Multi-marketplace with one account
- Automatic daily inventory reports
- Perfect for large catalogs

## Installation

Install via composer:

```bash
composer require sashalenz/ebay-mip
```

Publish configuration and migrations:

```bash
php artisan vendor:publish --tag="ebay-mip-config"
php artisan vendor:publish --tag="ebay-mip-migrations"
php artisan migrate
```

## Configuration

Configure SFTP credentials in `.env`:

```env
# MIP SFTP Connection
MIP_SFTP_HOST=sftp.mip.ebay.com
MIP_SFTP_USERNAME=your-mip-username
MIP_SFTP_PASSWORD=your-mip-password
MIP_SFTP_PORT=22

# Automatic Upload (Optional)
MIP_UPLOAD_ENABLED=true
MIP_UPLOAD_MODE=sftp
MIP_UPLOAD_SCHEDULE="0 2 * * *"

# Feed Storage
MIP_FEEDS_PATH=storage/mip/feeds
MIP_FEEDS_COMPRESSION=true
MIP_FEEDS_RETENTION_DAYS=30

# Validation
MIP_VALIDATION_STRICT=true
MIP_VALIDATION_THROW=true
```

## Quick Start

### Generate Product Feed (CSV)

```php
use Sashalenz\EbayMip\Builders\ProductFeedBuilder;
use Sashalenz\EbayMip\Data\Product\ProductData;
use Sashalenz\EbayMip\Enums\FeedFormat;

$feed = ProductFeedBuilder::make()
    ->format(FeedFormat::CSV)
    ->addProducts(
        Product::all()->map(fn($p) => ProductData::from([
            'sku' => $p->sku,
            'title' => $p->name,
            'description' => $p->description,
            'price' => $p->price,
            'quantity' => $p->stock,
            'categoryId' => $p->ebay_category_id,
            'images' => $p->images,
        ]))
    )
    ->validate() // Validates against eBay rules
    ->save(); // Saves to storage/mip/feeds/

echo "Feed saved: {$feed}";
```

### Upload to SFTP

```php
use Sashalenz\EbayMip\Upload\SftpUploader;

$uploader = app(SftpUploader::class);
$uploader->upload(
    storage_path('mip/feeds/product_20251014.csv'),
    '/inbound/product.csv'
);
```

### Automatic Upload via Scheduler

Enable automatic uploads in `.env`:

```env
MIP_UPLOAD_ENABLED=true
MIP_UPLOAD_SCHEDULE="0 2 * * *"
```

Feeds will be automatically uploaded daily at 2 AM. Or trigger manually:

```bash
php artisan mip:upload-feeds
```

## Available Feed Types

### 1. Product Feed

Upload new products or update existing listings:

```php
use Sashalenz\EbayMip\Builders\ProductFeedBuilder;

ProductFeedBuilder::make()
    ->format(FeedFormat::CSV) // or FeedFormat::XML
    ->addProduct(ProductData::from([
        'sku' => 'PROD-001',
        'title' => 'Vintage Camera',
        'description' => 'Classic 35mm film camera...',
        'price' => '99.99',
        'quantity' => 10,
        'categoryId' => '625',
        'images' => [
            'https://example.com/img1.jpg',
            'https://example.com/img2.jpg',
        ],
        'itemSpecifics' => [
            'Brand' => 'Canon',
            'Model' => 'AE-1',
            'Condition' => 'Used',
        ],
    ]))
    ->save()
    ->upload(); // Optional: upload immediately
```

### 2. Availability Feed

Update inventory quantities:

```php
use Sashalenz\EbayMip\Builders\AvailabilityFeedBuilder;

AvailabilityFeedBuilder::make()
    ->format(FeedFormat::CSV)
    ->addAvailability(AvailabilityData::from([
        'sku' => 'PROD-001',
        'quantity' => 5,
    ]))
    ->save();
```

### 3. Fulfillment Feed

Upload shipping/tracking information:

```php
use Sashalenz\EbayMip\Builders\FulfillmentFeedBuilder;

FulfillmentFeedBuilder::make()
    ->format(FeedFormat::CSV)
    ->addFulfillment(FulfillmentData::from([
        'orderId' => '12-34567-89012',
        'trackingNumber' => '1Z999AA10123456784',
        'carrier' => 'UPS',
        'shipDate' => '2025-10-14',
    ]))
    ->save();
```

### 4. Distribution Feed

Multi-location inventory:

```php
DistributionFeedBuilder::make()
    ->addDistribution(DistributionData::from([
        'sku' => 'PROD-001',
        'locationId' => 'WAREHOUSE_01',
        'quantity' => 100,
    ]))
    ->save();
```

### 5. Combined Feed

Most efficient - all inventory data in one feed:

```php
CombinedFeedBuilder::make()
    ->addProduct($productData)
    ->addDistribution($distributionData)
    ->addAvailability($availabilityData)
    ->save();
```

### 6. Order Report Feed

Download orders from MIP:

```bash
php artisan mip:download:orders
```

## Validation Rules

The package enforces eBay MIP validation rules:

- **Title**: Max 80 characters, no HTML tags
- **Description**: Max 500,000 characters
- **Price**: Positive decimal with 2 decimal places
- **Images**: HTTPS only, max 12 images, valid URLs
- **SKU**: Max 50 characters, alphanumeric
- **Quantity**: Non-negative integer
- **Category**: Valid eBay category ID

## Artisan Commands

### Generate Product Feed

```bash
php artisan mip:generate:product --format=csv
```

### Upload Feeds

```bash
php artisan mip:upload-feeds
```

### Download Order Reports

```bash
php artisan mip:download:orders
```

### Test SFTP Connection

```bash
php artisan mip:test-connection
```

## Laravel Events

Listen to feed lifecycle events in your `EventServiceProvider`:

```php
use Sashalenz\EbayMip\Events\FeedGeneratedEvent;
use Sashalenz\EbayMip\Events\FeedUploadedEvent;
use Sashalenz\EbayMip\Events\FeedUploadFailedEvent;

protected $listen = [
    FeedGeneratedEvent::class => [
        NotifyAdminFeedGenerated::class,
    ],
    FeedUploadedEvent::class => [
        LogFeedUpload::class,
        NotifySuccess::class,
    ],
    FeedUploadFailedEvent::class => [
        AlertAdminUploadFailed::class,
        RetryUpload::class,
    ],
];
```

## Upload History

Query upload history:

```php
use Sashalenz\EbayMip\Models\MipFeedUpload;

// Get recent uploads
$uploads = MipFeedUpload::orderBy('created_at', 'desc')->limit(10)->get();

// Get failed uploads
$failed = MipFeedUpload::failed()->get();

// Get uploads by feed type
$productUploads = MipFeedUpload::where('feed_type', 'product')->get();
```

## Manual vs Automatic Mode

### Automatic Mode (Recommended)

```env
MIP_UPLOAD_ENABLED=true
MIP_UPLOAD_MODE=sftp
```

Feeds are automatically uploaded via Laravel Scheduler.

### Manual Mode

```env
MIP_UPLOAD_MODE=manual
```

Generate feeds locally, upload manually when ready:

```php
$feed = ProductFeedBuilder::make()->save();
// Upload manually via SFTP client or MIP GUI
```

## Response File Parsing

Parse MIP response files to check for errors:

```php
use Sashalenz\EbayMip\Parsers\ResponseFileParser;

$parser = app(ResponseFileParser::class);
$response = $parser->parse(storage_path('mip/responses/product_response.csv'));

echo "Total: {$response->totalRecords}\n";
echo "Success: {$response->successRecords}\n";
echo "Errors: {$response->errorRecords}\n";

foreach ($response->errors as $sku => $error) {
    echo "SKU {$sku}: {$error}\n";
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Security Vulnerabilities

If you discover a security vulnerability, please send an email to sasha.lenz@gmail.com.

## Credits

- [Oleksandr Petrovskyi](https://github.com/sashalenz)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

