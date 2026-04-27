<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            $table->string('phase')->nullable()->after('user_id');
        });

        // Migrate existing data
        DB::table('consents')->where('signal', 'no_questions')->update(['phase' => 'clarification']);
        DB::table('consents')->where('signal', 'no_reaction')->update(['phase' => 'reaction']);
        DB::table('consents')->where('signal', 'no_objection')->update(['phase' => 'objection']);
        
        // For abstentions, we try to guess based on the decision status, 
        // but it's better than nothing for existing data.
        DB::statement("UPDATE consents 
            SET phase = d.status
            FROM decision_versions dv, decisions d
            WHERE consents.decision_version_id = dv.id
            AND dv.decision_id = d.id
            AND consents.signal = 'abstention' 
            AND consents.phase IS NULL");

        Schema::table('consents', function (Blueprint $table) {
            $table->dropUnique(['decision_version_id', 'user_id']);
            $table->unique(['decision_version_id', 'user_id', 'phase']);
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            $table->dropUnique(['decision_version_id', 'user_id', 'phase']);
            $table->unique(['decision_version_id', 'user_id']);
            $table->dropColumn('phase');
        });
    }
};
