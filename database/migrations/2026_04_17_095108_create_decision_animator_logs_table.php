<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_animator_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('decision_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('animator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('removed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_animator_logs');
    }
};
