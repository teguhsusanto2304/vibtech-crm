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
        Schema::create('email_batches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // e.g., "Marketing Newsletter Q3"
            $table->integer('total_recipients');
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0); // Track failures too
            $table->string('status')->default('pending'); // pending, in_progress, completed, failed
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who initiated it
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_batches');
    }
};
