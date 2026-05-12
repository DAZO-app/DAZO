<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circle_invite_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('circle_id')->constrained()->cascadeOnDelete();
            $table->string('token', 80)->unique();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->timestamp('expires_at');
            $table->integer('max_uses')->nullable();
            $table->integer('use_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circle_invite_links');
    }
};
