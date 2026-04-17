<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('circle_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft');
            $table->string('title');
            $table->string('visibility')->default('public');
            $table->integer('priority')->default(0);
            $table->boolean('emergency_mode')->default(false);
            $table->timestamp('objection_round_deadline')->nullable();
            $table->foreignUuid('model_id')->nullable()->constrained('decision_models')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
