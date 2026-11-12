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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->string('address');
            $table->timestamps();
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('supplier_id');
            $table->enum('transaction_type', ['instock', 'outstock']);
            $table->integer('quantity');
            $table->timestamps();
    
            $table->foreign('inventory_id')->references('id')->on('inventories');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers_and_inventory_transactions_tables');
    }
};
