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
        Schema::create('submit_claim_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submit_claim_id')->constrained('submit_claims')->onDelete('cascade'); // Link to the main claim
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SGD');
            $table->foreignId('claim_type_id')->constrained('claim_types')->onDelete('cascade'); 
            $table->integer('data_status')->default(1);
            $table->date('start_at');
            $table->date('end_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submit_claim_items');
    }
};
