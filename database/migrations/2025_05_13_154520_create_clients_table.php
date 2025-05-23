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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('company', 150);
            $table->string('position', 100)->nullable();
            $table->string('email')->unique();
            $table->string('office_number', 20);
            $table->string('mobile_number', 20)->nullable();
            $table->string('job_title', 100)->nullable();

            // Relations
            $table->foreignId('industry_category_id')->nullable()->constrained('industry_categories')->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('sales_person_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('contact_for_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('image_path')->nullable(); // Upload Image
            $table->string('remark')->nullable();
            $table->boolean('is_editable')->default(0);
            $table->boolean('is_deletable')->default(0);
            $table->integer('data_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
