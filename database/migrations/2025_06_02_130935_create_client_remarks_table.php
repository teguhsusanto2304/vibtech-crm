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
        Schema::create('client_remarks', function (Blueprint $table) {
            $table->id(); // Primary key for remarks table
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Foreign key to clients table
            $table->text('content'); // The actual remark/note text
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Who made the remark
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_remarks');
    }
};
