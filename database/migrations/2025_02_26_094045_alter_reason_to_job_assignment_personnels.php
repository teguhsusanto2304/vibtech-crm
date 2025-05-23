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
        Schema::table('job_assignment_personnels', function (Blueprint $table) {
            $table->string('reason', 100)->nullable();
            $table->date('purpose_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignment_personnels', function (Blueprint $table) {
            //
        });
    }
};
