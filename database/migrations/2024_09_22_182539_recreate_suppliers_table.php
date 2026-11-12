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
        Schema::dropIfExists('inventory_transactions'); // Drop dependent table first

        Schema::dropIfExists('suppliers'); // Drop suppliers table now

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('contact', 20); // 20 numeric characters
            $table->string('address', 100);
            $table->timestamps();
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('transaction_type', ['instock', 'outstock']); // Instock for adding, outstock for returning
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('suppliers');
    }
};
