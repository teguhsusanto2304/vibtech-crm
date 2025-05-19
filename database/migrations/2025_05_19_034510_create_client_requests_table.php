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
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('name',150);
            $table->string('company',150);
            $table->string('position',100)->nullable();
            $table->string('email');
            $table->string('office_number',20);
            $table->string('mobile_number',20)->nullable();
            $table->string('job_title',100)->nullable();

            // Relations
           $table->foreignId('industry_category_id')->nullable()->constrained('industry_categories')->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('sales_person_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('image_path')->nullable(); // Upload Image
            $table->string('delete_reason')->nullable();
            $table->integer('data_status')->default(1);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_requests');
    }
};
