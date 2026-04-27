<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_drawers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default cash drawers
        DB::table('cash_drawers')->insert([
            ['name' => 'Kasir Shift Pagi',   'description' => 'Shift 06:00 - 14:00', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kasir Shift Siang',  'description' => 'Shift 14:00 - 22:00', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kasir Shift Malam',  'description' => 'Shift 22:00 - 06:00', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_drawers');
    }
};
