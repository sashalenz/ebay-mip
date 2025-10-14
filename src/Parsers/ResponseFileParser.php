<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Parsers;

use League\Csv\Reader;
use Sashalenz\EbayMip\Data\Response\FeedResponseData;

/**
 * Response File Parser
 *
 * Parses MIP response files to extract success/error information.
 */
class ResponseFileParser
{
    /**
     * Parse response file
     */
    public function parse(string $filePath): FeedResponseData
    {
        if (! file_exists($filePath)) {
            throw new \InvalidArgumentException("Response file not found: {$filePath}");
        }

        // Detect format based on extension
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        return match ($extension) {
            'csv' => $this->parseCsv($filePath),
            'xml' => $this->parseXml($filePath),
            default => throw new \InvalidArgumentException("Unsupported response file format: {$extension}"),
        };
    }

    /**
     * Parse CSV response file
     */
    protected function parseCsv(string $filePath): FeedResponseData
    {
        $csv = Reader::createFromPath($filePath);
        $csv->setHeaderOffset(0);

        $records = iterator_to_array($csv->getRecords());
        $errors = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($records as $record) {
            $status = $record['Status'] ?? $record['status'] ?? '';

            if (str_contains(strtolower($status), 'success')) {
                $successCount++;
            } else {
                $errorCount++;
                $sku = $record['SKU'] ?? $record['sku'] ?? 'unknown';
                $error = $record['Error'] ?? $record['error'] ?? 'Unknown error';
                $errors[$sku] = $error;
            }
        }

        return FeedResponseData::from([
            'totalRecords' => count($records),
            'successRecords' => $successCount,
            'errorRecords' => $errorCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Parse XML response file
     */
    protected function parseXml(string $filePath): FeedResponseData
    {
        $xml = simplexml_load_file($filePath);
        $errors = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($xml->children() as $record) {
            $status = (string) ($record->Status ?? '');

            if (str_contains(strtolower($status), 'success')) {
                $successCount++;
            } else {
                $errorCount++;
                $sku = (string) ($record->SKU ?? 'unknown');
                $error = (string) ($record->Error ?? 'Unknown error');
                $errors[$sku] = $error;
            }
        }

        return FeedResponseData::from([
            'totalRecords' => $successCount + $errorCount,
            'successRecords' => $successCount,
            'errorRecords' => $errorCount,
            'errors' => $errors,
        ]);
    }
}
