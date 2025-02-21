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
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('branch_office',100)->nullable();
            $table->string('path_image',200)->nullable();
            $table->string('user_number',20)->nullable();
            $table->date('joined_at')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone_number',15)->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'position', 'branch_office']);
        });
    }
};
