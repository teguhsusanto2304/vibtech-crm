<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //$table->dropForeign(['position_level_id']);

            // Ensure position_level_id is nullable before adding constraint
            $table->unsignedBigInteger('position_level_id')->nullable()->change();
            //$table->dropForeign(['department_id']);

            // Ensure position_level_id is nullable before adding constraint
            $table->unsignedBigInteger('department_id')->nullable()->change();
            $table->foreign('position_level_id')
                ->references('id')
                ->on('position_levels')
                ->onDelete('cascade');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
