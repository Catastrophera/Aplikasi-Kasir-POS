<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flow_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pemasukan', 'pemasukan_lain', 'pengeluaran', 'pengeluaran_lain']);
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->date('date');
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flow_entries');
    }
};
