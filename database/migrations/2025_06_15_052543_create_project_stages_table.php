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
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            // Foreign Key to the 'projects' table
            $table->foreignId('project_id')
                  ->constrained('projects') // Assumes your projects table is named 'projects'
                  ->onDelete('cascade');   // If a project is deleted, its stages are also deleted

            // Foreign Key to the 'kanban_stages' table
            // This links to your predefined kanban stages (e.g., 'Design', 'Fabrication')
            $table->foreignId('kanban_stage_id')
                  ->constrained('kanban_stages') // Assumes your kanban_stages table is named 'kanban_stages'
                  ->onDelete('restrict'); // Or 'cascade' if deleting a kanban_stage should delete associated project_stages

            // Specific status for this project's instance of the stage
            $table->integer('data_status')->default(1); // e.g., 'pending', 'in_progress', 'completed', 'on_hold'

            // Optional: When this specific project stage was completed
            $table->timestamp('completed_at')->nullable();

            // Optional: show comploted task counting
            $table->integer('completed_task')->nullable()->default(0);

             // Optional: show incomploted task counting
            $table->integer('incompleted_task')->nullable()->default(0);

            // Optional: Any specific notes for this stage within the project
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stages');
    }
};
