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
        Schema::create('sales_forecast_individual_personals', function (Blueprint $table) {
            $table->id();
                        $table->foreignId('sf_individual_id')
                  ->constrained('sales_foreacast_individuallies') // Assumes your main table is 'sales_forecasts'
                  ->onDelete('cascade');
            $table->foreignId('personal_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_forecast_individual_personals');
    }
};
