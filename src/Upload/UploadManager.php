<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Upload;

use Illuminate\Support\Facades\Log;
use Sashalenz\EbayMip\Models\MipFeedUpload;

/**
 * Upload Manager
 *
 * Manages feed upload queue with retry logic.
 */
class UploadManager
{
    protected SftpUploader $uploader;

    protected int $maxAttempts;

    protected int $retryDelay;

    public function __construct(SftpUploader $uploader)
    {
        $this->uploader = $uploader;
        $this->maxAttempts = config('ebay-mip.upload.retry_attempts', 3);
        $this->retryDelay = config('ebay-mip.upload.retry_delay', 60);
    }

    /**
     * Upload pending feeds
     */
    public function uploadPending(): array
    {
        $pending = MipFeedUpload::pending()->get();
        $results = [];

        foreach ($pending as $upload) {
            $result = $this->uploadWithRetry($upload);
            $results[] = [
                'id' => $upload->id,
                'filename' => $upload->filename,
                'success' => $result,
            ];
        }

        return $results;
    }

    /**
     * Upload single feed with retry logic
     */
    public function uploadWithRetry(MipFeedUpload $upload, int $attempt = 1): bool
    {
        try {
            $upload->markAsUploading();

            $remotePath = $this->getRemotePath($upload);

            $success = $this->uploader->upload($upload->local_path, $remotePath);

            if ($success) {
                $upload->markAsUploaded($remotePath);

                return true;
            }

            throw new \Exception('Upload returned false');
        } catch (\Exception $e) {
            Log::warning("MIP upload attempt {$attempt} failed", [
                'filename' => $upload->filename,
                'error' => $e->getMessage(),
            ]);

            if ($attempt < $this->maxAttempts) {
                sleep($this->retryDelay * $attempt); // Exponential backoff

                return $this->uploadWithRetry($upload, $attempt + 1);
            }

            $upload->markAsFailed($e->getMessage());

            return false;
        }
    }

    /**
     * Get remote path for upload
     */
    protected function getRemotePath(MipFeedUpload $upload): string
    {
        return "/inbound/{$upload->filename}";
    }

    /**
     * Create upload record
     */
    public function createUploadRecord(
        string $feedType,
        string $feedFormat,
        string $filename,
        string $localPath,
        int $recordsCount
    ): MipFeedUpload {
        return MipFeedUpload::create([
            'feed_type' => $feedType,
            'feed_format' => $feedFormat,
            'filename' => $filename,
            'local_path' => $localPath,
            'records_count' => $recordsCount,
            'status' => 'pending',
        ]);
    }
}
