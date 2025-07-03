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
        Schema::create('role_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->unique()->onDelete('cascade');
            $table->integer('data_status'); // e.g., 'active', 'inactive', 'pending'
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_statuses');
    }
};
