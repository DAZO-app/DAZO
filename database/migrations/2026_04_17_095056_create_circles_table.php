<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('circles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('open');
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('circles', function (Blueprint $table) {
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('circles')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circles');
    }
};