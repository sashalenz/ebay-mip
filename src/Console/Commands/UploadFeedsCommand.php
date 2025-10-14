<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Console\Commands;

use Illuminate\Console\Command;
use Sashalenz\EbayMip\Upload\UploadManager;

/**
 * Upload Feeds Command
 *
 * Uploads pending feeds to MIP via SFTP.
 */
class UploadFeedsCommand extends Command
{
    protected $signature = 'mip:upload-feeds';

    protected $description = 'Upload pending MIP feeds to SFTP';

    public function handle(UploadManager $manager): int
    {
        $this->info('Uploading pending MIP feeds...');

        $results = $manager->uploadPending();

        if (empty($results)) {
            $this->info('No pending feeds to upload.');

            return self::SUCCESS;
        }

        $successful = 0;
        $failed = 0;

        foreach ($results as $result) {
            if ($result['success']) {
                $this->info("✓ Uploaded: {$result['filename']}");
                $successful++;
            } else {
                $this->error("✗ Failed: {$result['filename']}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Summary: {$successful} successful, {$failed} failed");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
