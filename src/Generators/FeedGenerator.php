<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Generators;

use Illuminate\Support\Facades\File;
use Sashalenz\EbayMip\Enums\FeedFormat;
use Sashalenz\EbayMip\Enums\FeedType;
use Sashalenz\EbayMip\Generators\Contracts\FeedGeneratorInterface;
use Sashalenz\EbayMip\Validation\FeedValidator;

/**
 * Feed Generator Base Class
 *
 * Abstract base for all MIP feed generators.
 */
abstract class FeedGenerator implements FeedGeneratorInterface
{
    protected FeedType $feedType;

    protected FeedFormat $format;

    protected array $data = [];

    protected FeedValidator $validator;

    protected ?string $generatedContent = null;

    /**
     * Generate feed content
     */
    abstract public function generate(): string;

    /**
     * Validate feed data
     */
    public function validate(): array
    {
        return $this->validator->validate($this->data);
    }

    /**
     * Save feed to disk
     */
    public function save(?string $path = null): string
    {
        if ($this->generatedContent === null) {
            $this->generatedContent = $this->generate();
        }

        $path = $path ?? $this->getDefaultPath();

        // Ensure directory exists
        $directory = dirname($path);
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Save file
        File::put($path, $this->generatedContent);

        // Compress if configured
        if (config('ebay-mip.feeds.compression', true)) {
            $this->compress($path);
        }

        return basename($path);
    }

    /**
     * Compress feed file to .zip
     */
    protected function compress(string $path): string
    {
        $zipPath = $path.'.zip';
        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($path, basename($path));
            $zip->close();

            // Delete original if not keeping
            if (! config('ebay-mip.feeds.keep_original', true)) {
                File::delete($path);
            }
        }

        return $zipPath;
    }

    /**
     * Get default storage path
     */
    protected function getDefaultPath(): string
    {
        $directory = config('ebay-mip.feeds.storage_path', storage_path('mip/feeds'));
        $filename = $this->generateFilename();

        return $directory.'/'.$filename;
    }

    /**
     * Generate filename with timestamp
     */
    protected function generateFilename(): string
    {
        $timestamp = now()->format('Ymd_His');

        return "{$this->feedType->value}_{$timestamp}.{$this->format->extension()}";
    }

    /**
     * Get feed content
     */
    public function getContent(): string
    {
        return $this->generatedContent ?? $this->generate();
    }

    /**
     * Set data for feed
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Add single record to feed
     */
    public function addRecord(mixed $record): static
    {
        $this->data[] = $record;

        return $this;
    }

    /**
     * Get record count
     */
    public function getRecordCount(): int
    {
        return count($this->data);
    }
}
