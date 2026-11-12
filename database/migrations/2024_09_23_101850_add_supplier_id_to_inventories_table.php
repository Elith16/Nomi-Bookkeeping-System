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
        Schema::table('inventories', function (Blueprint $table) {
            // Add the supplier_id foreign key column to inventories table
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Drop the foreign key and the column
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
