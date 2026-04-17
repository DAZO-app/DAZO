<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_labels', function (Blueprint $table) {
            $table->foreignUuid('decision_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('label_id')->constrained()->cascadeOnDelete();

            $table->primary(['decision_id', 'label_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_labels');
    }
};
