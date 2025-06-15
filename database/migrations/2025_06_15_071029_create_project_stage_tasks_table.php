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
        Schema::create('project_stage_tasks', function (Blueprint $table) {
            $table->id();
            // Foreign Key to the 'project_stages' table
            $table->foreignId('project_stage_id')
                  ->constrained('project_stages') // Assumes your project_stages table
                  ->onDelete('cascade');         // If a project stage is deleted, its tasks are also deleted

            // Optional: Foreign Key for task assignee
            $table->foreignId('assigned_to_user_id')
                    ->nullable()
                  ->constrained('users') // Assumes your users table
                  ->onDelete('set null'); // If user is deleted, assignee becomes null

            $table->string('name');         // Name/title of the task
            $table->text('description')->nullable(); // Detailed description of the task
            $table->string('data_status')->default(1); // e.g., 'pending', 'in_progress', 'completed', 'blocked'

            $table->integer('progress_percentage')->default(0);
            
            // Optional: Due date for the task
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();

            // Optional: When this specific task was completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stage_tasks');
    }
};
