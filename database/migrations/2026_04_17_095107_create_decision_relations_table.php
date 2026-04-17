<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_relations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('source_decision_id')->constrained('decisions')->cascadeOnDelete();
            $table->foreignUuid('target_decision_id')->constrained('decisions')->cascadeOnDelete();
            $table->string('relation_type');
            $table->timestamps();

            $table->unique(['source_decision_id', 'target_decision_id', 'relation_type'], 'decision_relations_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_relations');
    }
};
