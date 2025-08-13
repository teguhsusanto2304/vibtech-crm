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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('adjust_type'); // 'increase' or 'decrease'
            $table->integer('quantity');
            $table->string('adjust_number',20);
            $table->string('for_or_from',100);
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Optional: to track who made the adjustment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
