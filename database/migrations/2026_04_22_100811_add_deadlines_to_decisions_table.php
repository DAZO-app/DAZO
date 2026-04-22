<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('decisions', function (Blueprint $table) {
            $table->timestamp('current_deadline')->nullable()->after('emergency_mode');
            $table->boolean('reminder_sent')->default(false)->after('current_deadline');
        });
    }

    public function down(): void
    {
        Schema::table('decisions', function (Blueprint $table) {
            $table->dropColumn(['current_deadline', 'reminder_sent']);
        });
    }
};
