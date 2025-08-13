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
        Schema::create('project_kanbans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('projects') // Assumes your projects table is named 'projects'
                  ->onDelete('cascade');
            $table->string('name',100);
            $table->boolean('data_status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_kanbans');
    }
};
