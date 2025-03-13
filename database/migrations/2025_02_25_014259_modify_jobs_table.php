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
        Schema::table('job_assignments', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);

            // Modify the column to be nullable
            $table->unsignedBigInteger('vehicle_id')->nullable()->change();

            // Re-add the foreign key constraint
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');

            $table->dropForeign(['job_type_id']);

            // Modify the column to be nullable
            $table->unsignedBigInteger('job_type_id')->nullable()->change();

            // Re-add the foreign key constraint
            $table->foreign('job_type_id')->references('id')->on('job_types')->onDelete('set null');
            $table->string('job_type')->after('job_type_id'); // Adjust position as needed
            $table->boolean('is_vehicle_require')->default(false)->before('vehicle_id');
            $table->boolean('is_publish')->default(false)->before('is_vehicle_require');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['vehicle_id']);

            // Revert back to non-nullable
            $table->unsignedBigInteger('vehicle_id')->nullable(false)->change();

            // Re-add the foreign key constraint
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            $table->dropForeign(['job_type_id']);

            // Revert back to non-nullable
            $table->unsignedBigInteger('job_type_id')->nullable(false)->change();

            // Re-add the foreign key constraint
            $table->foreign('job_type_id')->references('id')->on('job_types')->onDelete('cascade');

            $table->dropColumn('job_type');
            $table->dropColumn('is_vehicle_require');
        });
    }
};
