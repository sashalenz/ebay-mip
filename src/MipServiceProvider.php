<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip;

use Illuminate\Console\Scheduling\Schedule;
use Sashalenz\EbayMip\Console\Commands\DownloadOrderReportsCommand;
use Sashalenz\EbayMip\Console\Commands\GenerateProductFeedCommand;
use Sashalenz\EbayMip\Console\Commands\TestSftpConnectionCommand;
use Sashalenz\EbayMip\Console\Commands\UploadFeedsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * eBay MIP Service Provider
 *
 * Registers the eBay MIP package with Laravel.
 */
class MipServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ebay-mip')
            ->hasConfigFile()
            ->hasMigration('create_mip_feed_uploads_table')
            ->hasCommands([
                UploadFeedsCommand::class,
                GenerateProductFeedCommand::class,
                DownloadOrderReportsCommand::class,
                TestSftpConnectionCommand::class,
            ]);
    }

    /**
     * Register package services
     */
    public function packageRegistered(): void
    {
        $this->app->singleton(MipManager::class, function ($app) {
            return new MipManager;
        });

        $this->app->alias(MipManager::class, 'ebay-mip');
    }

    /**
     * Boot package services
     */
    public function packageBooted(): void
    {
        // Register scheduled tasks if automatic upload is enabled
        if (config('ebay-mip.upload.enabled', false)) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);

                $schedule->command(UploadFeedsCommand::class)
                    ->cron(config('ebay-mip.upload.schedule', '0 2 * * *'))
                    ->onSuccess(function () {
                        \Illuminate\Support\Facades\Log::info('MIP feeds uploaded successfully via scheduler');
                    })
                    ->onFailure(function () {
                        \Illuminate\Support\Facades\Log::error('MIP feeds upload failed via scheduler');
                    });
            });
        }
    }
}
