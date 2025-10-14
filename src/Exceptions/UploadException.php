<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Exceptions;

use Exception;

/**
 * Upload Exception
 *
 * Thrown when feed upload fails.
 */
class UploadException extends Exception
{
    public static function sftpConnectionFailed(string $message): self
    {
        return new self("SFTP connection failed: {$message}");
    }

    public static function uploadFailed(string $filename, string $message): self
    {
        return new self("Failed to upload {$filename}: {$message}");
    }
}
