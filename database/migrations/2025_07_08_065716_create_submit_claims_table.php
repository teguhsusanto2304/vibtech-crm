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
        Schema::create('submit_claims', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique(); // Unique serial number for the main claim
            $table->date('claim_date');
            $table->string('description')->nullable();
            $table->foreignId('staff_id')->constrained('users'); // Assuming staff are users
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('SGD');
            $table->integer('data_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submit_claims');
    }
};
