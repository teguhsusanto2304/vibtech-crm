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
        Schema::create('submit_claim_files', function (Blueprint $table) {
            $table->id();
            // Foreign Key to the 'projects' table
            $table->foreignId('submit_claim_item_id')
                  ->constrained('submit_claim_items') // Assumes your projects table is named 'projects'
                  ->onDelete('cascade');   // If a project is deleted, its files are also deleted

            $table->string('file_name');         // Original name of the file (e.g., "Project_Report_V1.pdf")
            $table->string('file_path');         // Path where the file is stored (e.g., "projects/1/reports/unique_hash.pdf")
            $table->string('mime_type')->nullable(); // MIME type of the file (e.g., "application/pdf", "image/jpeg")
            $table->unsignedBigInteger('file_size')->nullable(); // Size in bytes

            $table->string('description')->nullable(); // Optional: A short description for the file

            // Optional: User who uploaded the file
            $table->foreignId('uploaded_by_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submit_claim_files');
    }
};
