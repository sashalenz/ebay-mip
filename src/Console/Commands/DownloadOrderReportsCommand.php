<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Console\Commands;

use Illuminate\Console\Command;
use Sashalenz\EbayMip\Upload\SftpUploader;

/**
 * Download Order Reports Command
 *
 * Downloads order reports from MIP SFTP.
 */
class DownloadOrderReportsCommand extends Command
{
    protected $signature = 'mip:download:orders {--directory=/outbound : Remote directory to download from}';

    protected $description = 'Download order reports from MIP SFTP';

    public function handle(SftpUploader $uploader): int
    {
        $this->info('Downloading order reports from MIP...');

        try {
            $directory = $this->option('directory');
            $files = $uploader->listFiles($directory);

            if (empty($files)) {
                $this->info('No order reports found.');

                return self::SUCCESS;
            }

            $localPath = storage_path('mip/orders');

            if (! is_dir($localPath)) {
                mkdir($localPath, 0755, true);
            }

            $downloaded = 0;

            foreach ($files as $file) {
                if ($file['type'] !== 'file') {
                    continue;
                }

                $remotePath = $file['path'];
                $localFile = $localPath.'/'.basename($remotePath);

                if ($uploader->download($remotePath, $localFile)) {
                    $this->info("✓ Downloaded: {$remotePath}");
                    $downloaded++;
                } else {
                    $this->error("✗ Failed: {$remotePath}");
                }
            }

            $this->newLine();
            $this->info("Downloaded {$downloaded} order reports");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to download order reports: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
