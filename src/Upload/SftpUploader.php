<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Upload;

use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use Sashalenz\EbayMip\Events\FeedUploadedEvent;
use Sashalenz\EbayMip\Events\FeedUploadFailedEvent;

/**
 * SFTP Uploader
 *
 * Handles SFTP operations for MIP feed uploads.
 */
class SftpUploader
{
    protected ?Filesystem $filesystem = null;

    protected array $config;

    public function __construct()
    {
        $this->config = config('ebay-mip.sftp');
    }

    /**
     * Connect to SFTP server
     */
    public function connect(): void
    {
        if ($this->filesystem !== null) {
            return;
        }

        $provider = new SftpConnectionProvider(
            host: $this->config['host'],
            username: $this->config['username'],
            password: $this->config['password'],
            port: $this->config['port'] ?? 22,
            timeout: $this->config['timeout'] ?? 30,
            root: $this->config['root'] ?? '/',
        );

        $adapter = new SftpAdapter($provider, $this->config['root'] ?? '/');
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * Upload file to SFTP
     */
    public function upload(string $localPath, string $remotePath): bool
    {
        try {
            $this->connect();

            $contents = file_get_contents($localPath);

            $this->filesystem->write($remotePath, $contents);

            event(new FeedUploadedEvent(
                filename: basename($localPath),
                remotePath: $remotePath,
                size: strlen($contents)
            ));

            Log::info('MIP feed uploaded successfully', [
                'local' => $localPath,
                'remote' => $remotePath,
            ]);

            return true;
        } catch (UnableToWriteFile $e) {
            event(new FeedUploadFailedEvent(
                filename: basename($localPath),
                error: $e->getMessage()
            ));

            Log::error('Failed to upload MIP feed', [
                'local' => $localPath,
                'remote' => $remotePath,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Download file from SFTP
     */
    public function download(string $remotePath, string $localPath): bool
    {
        try {
            $this->connect();

            $contents = $this->filesystem->read($remotePath);
            file_put_contents($localPath, $contents);

            Log::info('MIP file downloaded successfully', [
                'remote' => $remotePath,
                'local' => $localPath,
            ]);

            return true;
        } catch (UnableToReadFile $e) {
            Log::error('Failed to download MIP file', [
                'remote' => $remotePath,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * List files in directory
     */
    public function listFiles(string $directory = '/'): array
    {
        $this->connect();

        try {
            $listing = $this->filesystem->listContents($directory);
            $files = [];

            foreach ($listing as $item) {
                $files[] = [
                    'path' => $item->path(),
                    'type' => $item->type(),
                    'size' => $item->fileSize() ?? 0,
                    'timestamp' => $item->lastModified() ?? time(),
                ];
            }

            return $files;
        } catch (\Exception $e) {
            Log::error('Failed to list MIP files', [
                'directory' => $directory,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Delete file from SFTP
     */
    public function delete(string $remotePath): bool
    {
        try {
            $this->connect();
            $this->filesystem->delete($remotePath);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete MIP file', [
                'remote' => $remotePath,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Test SFTP connection
     */
    public function testConnection(): bool
    {
        try {
            $this->connect();
            $this->filesystem->listContents('/');

            return true;
        } catch (\Exception $e) {
            Log::error('SFTP connection test failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
