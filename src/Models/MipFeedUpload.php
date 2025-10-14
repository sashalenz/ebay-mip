<?php

declare(strict_types=1);

namespace Sashalenz\EbayMip\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * MIP Feed Upload Model
 *
 * Tracks feed upload history and status.
 *
 * @property int $id
 * @property string $feed_type
 * @property string $feed_format
 * @property string $filename
 * @property string $local_path
 * @property string|null $remote_path
 * @property string $status
 * @property string|null $error_message
 * @property int|null $records_count
 * @property \Carbon\Carbon|null $uploaded_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MipFeedUpload extends Model
{
    protected $table = 'mip_feed_uploads';

    protected $guarded = [];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'records_count' => 'integer',
    ];

    /**
     * Scope: Get pending uploads
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get uploaded feeds
     */
    public function scopeUploaded(Builder $query): Builder
    {
        return $query->where('status', 'uploaded');
    }

    /**
     * Scope: Get failed uploads
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Get by feed type
     */
    public function scopeByType(Builder $query, string $feedType): Builder
    {
        return $query->where('feed_type', $feedType);
    }

    /**
     * Mark as uploading
     */
    public function markAsUploading(): void
    {
        $this->update(['status' => 'uploading']);
    }

    /**
     * Mark as uploaded
     */
    public function markAsUploaded(string $remotePath): void
    {
        $this->update([
            'status' => 'uploaded',
            'remote_path' => $remotePath,
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
