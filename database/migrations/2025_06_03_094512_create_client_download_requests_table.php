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
        Schema::create('client_download_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('total_data');
            $table->foreignId('request_id')->constrained('users');
            $table->foreignId('approved_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('data_status'); //0 = request,1=approved,3=rejected
            $table->string('file_type',50);
            $table->string('unique_token',100)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('request_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_download_requests');
    }
};
