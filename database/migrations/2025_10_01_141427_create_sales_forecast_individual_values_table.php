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
        Schema::create('sales_forecast_individual_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sf_individual_id')
                  ->constrained('sales_foreacast_individuallies') // Assumes your main table is 'sales_forecasts'
                  ->onDelete('cascade');
            $table->string('company',100);
            $table->integer('sales_forecast_month'); 
            $table->integer('sales_forecast_year'); 
            $table->string('sales_forecast_currency',3);

            // The actual forecast amount (using decimal for currency)
            $table->decimal('amount', 15, 2)->default(0.00);
            
            // Add a unique constraint to ensure one value per individual/month/forecast
            $table->string('data_type', 50)->default('monthly_total'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_forecast_individual_values');
    }
};
