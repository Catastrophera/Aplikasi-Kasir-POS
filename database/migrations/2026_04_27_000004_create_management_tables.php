<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Kontak (Pelanggan & Supplier)
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('type', ['customer', 'supplier', 'both'])->default('customer');
            $table->timestamps();
        });

        // Tabel Pembelian (Belanja ke Supplier)
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('set null');
            $table->decimal('total_amount', 15, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        // Tabel Item Pembelian
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('contacts');
    }
};
