<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mip_feed_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('feed_type')->index();
            $table->string('feed_format');
            $table->string('filename');
            $table->string('local_path');
            $table->string('remote_path')->nullable();
            $table->enum('status', ['pending', 'uploading', 'uploaded', 'failed'])->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->integer('records_count')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mip_feed_uploads');
    }
};
