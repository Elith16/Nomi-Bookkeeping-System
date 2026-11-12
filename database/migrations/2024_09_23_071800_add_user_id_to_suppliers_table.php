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
        Schema::table('suppliers', function (Blueprint $table) {
            // Add user_id if it doesn't exist
            if (!Schema::hasColumn('suppliers', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Safely drop the foreign key only if it exists
            if (Schema::hasColumn('suppliers', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
        });
    }
};
