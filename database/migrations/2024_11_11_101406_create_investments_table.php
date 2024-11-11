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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('investment_instrument_id')->constrained()->onDelete('cascade'); // Foreign key to investment_instruments table
            $table->date('date');
            $table->text('description')->nullable();
            $table->enum('type', ['buy', 'sell']);
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            //convertion rate from euro to ron
            $table->decimal('conversion_rate', 10, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
