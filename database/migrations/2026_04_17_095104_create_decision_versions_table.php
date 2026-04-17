<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('decision_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('previous_version_id')->nullable();
            $table->integer('version_number');
            $table->boolean('is_current')->default(false);
            $table->text('content');
            $table->text('change_reason')->nullable();
            $table->timestamps();
        });

        Schema::table('decision_versions', function (Blueprint $table) {
            $table->foreign('previous_version_id')
                  ->references('id')
                  ->on('decision_versions')
                  ->nullOnDelete();
        });
   }

    public function down(): void
    {
        Schema::dropIfExists('decision_versions');
    }
};
