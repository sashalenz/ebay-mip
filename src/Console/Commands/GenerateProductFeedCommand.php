<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Console\Commands;

use Illuminate\Console\Command;
use Sashalenz\EbayMip\Enums\FeedFormat;

/**
 * Generate Product Feed Command
 *
 * Example command for generating product feeds.
 * Users should customize this based on their data source.
 */
class GenerateProductFeedCommand extends Command
{
    protected $signature = 'mip:generate:product {--format=csv : Feed format (csv or xml)}';

    protected $description = 'Generate product feed for MIP';

    public function handle(): int
    {
        $format = FeedFormat::from($this->option('format'));

        $this->info("Generating product feed ({$format->value})...");

        try {
            // Example: Users should implement their own data source
            $this->warn('This is an example command. Implement your own data source.');
            $this->info('Example:');
            $this->line('');
            $this->line('$products = Product::all()->map(fn($p) => ProductData::from([...]));');
            $this->line('$feed = ProductFeedBuilder::make()');
            $this->line('    ->format($format)');
            $this->line('    ->addProducts($products)');
            $this->line('    ->save();');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate feed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
