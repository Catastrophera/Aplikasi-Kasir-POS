<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create raw_materials table
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('stock', 10, 2)->default(0.00);
            $table->string('unit')->default('pcs'); // e.g. gr, pcs, ml
            $table->decimal('min_stock', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 2. Create recipes table
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(1.00); // quantity of raw material consumed per 1 menu portion
            $table->timestamps();
        });

        // 3. Seed default raw materials
        DB::table('raw_materials')->insert([
            [
                'name' => 'Daging Sapi (Beef)',
                'stock' => 5000.00,
                'unit' => 'gr',
                'min_stock' => 1000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tortilla Kebab',
                'stock' => 100.00,
                'unit' => 'pcs',
                'min_stock' => 20.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Saus Tomat & Sambal',
                'stock' => 2000.00,
                'unit' => 'ml',
                'min_stock' => 500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Saus Cheese',
                'stock' => 1000.00,
                'unit' => 'ml',
                'min_stock' => 200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Saus BBQ',
                'stock' => 1000.00,
                'unit' => 'ml',
                'min_stock' => 200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('raw_materials');
    }
};
