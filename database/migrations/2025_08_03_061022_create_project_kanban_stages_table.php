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
        Schema::create('project_kanban_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('projects') // Assumes your project_stages table
                  ->onDelete('cascade'); 
            $table->string('name',100);
            $table->string('description')->nullable();
            $table->integer('data_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_kanban_stages');
    }
};
