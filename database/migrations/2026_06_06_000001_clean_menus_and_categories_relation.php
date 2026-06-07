<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add category_id column
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
        });

        // 2. Map existing category string values to categories table ID
        $menus = DB::table('menus')->get();
        foreach ($menus as $menu) {
            if ($menu->category) {
                $category = DB::table('categories')->where('name', $menu->category)->first();
                if ($category) {
                    DB::table('menus')->where('id', $menu->id)->update(['category_id' => $category->id]);
                } else {
                    // Create category if it doesn't exist
                    $catId = DB::table('categories')->insertGetId([
                        'name' => $menu->category,
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('menus')->where('id', $menu->id)->update(['category_id' => $catId]);
                }
            }
        }

        // 3. Drop category string column
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        // 1. Recreate category string column
        Schema::table('menus', function (Blueprint $table) {
            $table->string('category')->default('Umum')->after('name');
        });

        // 2. Map category_id back to category string
        $menus = DB::table('menus')->get();
        foreach ($menus as $menu) {
            if ($menu->category_id) {
                $category = DB::table('categories')->where('id', $menu->category_id)->first();
                if ($category) {
                    DB::table('menus')->where('id', $menu->id)->update(['category' => $category->name]);
                }
            }
        }

        // 3. Drop category_id foreign key and column
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
