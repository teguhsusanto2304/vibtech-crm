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
        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('job_record_id',15);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('job_type_id')->constrained('job_types');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->string('business_name',100);
            $table->string('business_address',200);
            $table->string('scope_of_work',300);
            $table->date('start_at');
            $table->date('end_at');
            $table->integer('job_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_assignments');
    }
};
