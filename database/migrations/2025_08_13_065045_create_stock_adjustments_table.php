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
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('adjustment_type'); // 'Increase Stock' atau 'Decrease Stock'
            $table->integer('quantity_adjusted');
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->string('po_number')->nullable();
            $table->string('source')->nullable(); // Untuk 'From' (nama perusahaan)
            $table->date('purchase_date')->nullable();
            $table->date('received_date')->nullable();
            $table->date('draw_out_date')->nullable();
            $table->text('notes')->nullable();           
            $table->tinyInteger('data_status')->default(1);  // Optional: to track who made the adjustment
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
