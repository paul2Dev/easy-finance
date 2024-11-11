<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the 'type' column in 'categories' table to add 'investment' to the enum
        DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('income', 'expense') DEFAULT 'expense'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the previous enum definition
        DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('income', 'expense') DEFAULT 'expense'");
    }
};
