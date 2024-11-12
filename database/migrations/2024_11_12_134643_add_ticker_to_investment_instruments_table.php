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
        Schema::table('investment_instruments', function (Blueprint $table) {
            $table->string('ticker')->nullable()->after('description'); // Add ticker column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_instruments', function (Blueprint $table) {
            $table->dropColumn('ticker'); // Remove ticker column
        });
    }
};
