<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('decision_version_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('uploader_id')->constrained('users')->cascadeOnDelete();
            $table->string('filename');
            $table->string('s3_path');
            $table->string('mime_type');
            $table->integer('size_bytes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
