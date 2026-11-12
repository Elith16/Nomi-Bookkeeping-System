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
        Schema::dropIfExists('inventory_transactions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // You can recreate the table here if needed
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('inventory_id');
            $table->integer('quantity');
            $table->enum('transaction_type', ['instock', 'outstock']);
            $table->timestamps();
        });
    }
};
