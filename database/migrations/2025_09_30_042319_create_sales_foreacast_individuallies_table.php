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
        Schema::create('sales_foreacast_individuallies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_forecasts_id')->constrained('sales_forecasts');
            $table->foreignId('individually_id')->constrained('individuallies')->onDelete('cascade');
            $table->integer('data_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_foreacast_individuallies');
    }
};
