<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Console\Commands;

use Illuminate\Console\Command;
use Sashalenz\EbayMip\Upload\SftpUploader;

/**
 * Test SFTP Connection Command
 *
 * Tests SFTP connection to MIP servers.
 */
class TestSftpConnectionCommand extends Command
{
    protected $signature = 'mip:test-connection';

    protected $description = 'Test SFTP connection to eBay MIP';

    public function handle(SftpUploader $uploader): int
    {
        $this->info('Testing SFTP connection to eBay MIP...');
        $this->newLine();

        $config = config('ebay-mip.sftp');

        $this->line("Host: {$config['host']}");
        $this->line("Username: {$config['username']}");
        $this->line("Port: {$config['port']}");
        $this->newLine();

        try {
            if ($uploader->testConnection()) {
                $this->info('✓ Connection successful!');

                return self::SUCCESS;
            }

            $this->error('✗ Connection failed (no exception but returned false)');

            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error('✗ Connection failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
