<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wiki_pages', function (Blueprint $table) {
            $table->foreignUuid('wiki_category_id')->nullable()->after('id')->constrained('wiki_categories')->onDelete('set null');
            $table->integer('order')->default(0)->after('category');
        });

        // Migrate existing data
        $categories = DB::table('wiki_pages')->whereNotNull('category')->distinct()->pluck('category');
        foreach ($categories as $index => $catName) {
            $catId = Str::uuid();
            DB::table('wiki_categories')->insert([
                'id' => $catId,
                'name' => $catName,
                'slug' => Str::slug($catName),
                'order' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('wiki_pages')->where('category', $catName)->update(['wiki_category_id' => $catId]);
        }

        Schema::table('wiki_pages', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wiki_pages', function (Blueprint $table) {
            $table->string('category')->nullable()->after('wiki_category_id');
        });

        // Optional: reverse data migration if needed
        $pages = DB::table('wiki_pages')->whereNotNull('wiki_category_id')->get();
        foreach ($pages as $page) {
            $catName = DB::table('wiki_categories')->where('id', $page->wiki_category_id)->value('name');
            DB::table('wiki_pages')->where('id', $page->id)->update(['category' => $catName]);
        }

        Schema::table('wiki_pages', function (Blueprint $table) {
            $table->dropForeign(['wiki_category_id']);
            $table->dropColumn(['wiki_category_id', 'order']);
        });
    }
};
