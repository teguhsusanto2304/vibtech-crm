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
        Schema::create('receiving_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_order_id')->constrained('receiving_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('quantity')->comment('Jumlah barang yang diterima');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_order_items');
    }
};
