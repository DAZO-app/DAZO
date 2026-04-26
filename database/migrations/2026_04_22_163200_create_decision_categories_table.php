<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the pivot table
        Schema::create('decision_categories', function (Blueprint $table) {
            $table->foreignUuid('decision_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['decision_id', 'category_id']);
        });

        // 2. Migrate existing data
        $decisionsWithCategory = DB::table('decisions')
            ->whereNotNull('category_id')
            ->select('id', 'category_id')
            ->get();

        foreach ($decisionsWithCategory as $decision) {
            DB::table('decision_categories')->insert([
                'decision_id' => $decision->id,
                'category_id' => $decision->category_id,
            ]);
        }

        // 3. Drop the old column
        Schema::table('decisions', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        // 1. Re-add the column
        Schema::table('decisions', function (Blueprint $table) {
            $table->uuid('category_id')->nullable()->after('circle_id');
        });

        // 2. Rollback data (best effort: take the first category)
        $pivots = DB::table('decision_categories')->get();
        foreach ($pivots as $pivot) {
            DB::table('decisions')
                ->where('id', $pivot->decision_id)
                ->whereNull('category_id')
                ->update(['category_id' => $pivot->category_id]);
        }

        // 3. Drop the pivot table
        Schema::dropIfExists('decision_categories');
    }
};
