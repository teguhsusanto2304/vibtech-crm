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
        Schema::create('job_assignment_personnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_assignment_id')->constrained('job_assignments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User represents personnel
            $table->integer('is_read')->default(0);
            $table->integer('assignment_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_assignment_personnels');
    }
};
