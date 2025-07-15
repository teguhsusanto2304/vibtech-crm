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
        Schema::create('submit_claim_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submit_claim_id')->constrained('submit_claims')->onDelete('cascade');
            $table->foreignId('approved_by_user_id')->constrained('users')->onDelete('restrict'); // User who approved/rejected
            $table->string('data_status'); // e.g., 'approved', 'rejected'
            $table->text('notes')->nullable(); // For rejection reason or approval notes
            $table->string('transfer_document_path')->nullable(); // Path to the uploaded document
            $table->dateTime('transfered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submit_claim_approvals');
    }
};
