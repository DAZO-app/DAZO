<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider');         // google, github, facebook, x, linkedin, gitlab, microsoft, apple, franceconnect
            $table->string('provider_id');      // Unique ID from the provider
            $table->text('provider_token')->nullable();
            $table->text('provider_refresh_token')->nullable();
            $table->json('provider_data')->nullable(); // Raw profile data for reference
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
