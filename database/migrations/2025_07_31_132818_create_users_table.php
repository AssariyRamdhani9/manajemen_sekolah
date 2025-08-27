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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // SERIAL PRIMARY KEY
            $table->string('full_name'); // VARCHAR(255)
            $table->string('email')->unique(); // VARCHAR(255) UNIQUE
            $table->string('password'); // VARCHAR(255)
            $table->string('role', 50); // VARCHAR(50)
            $table->string('phone_number', 20)->nullable(); // VARCHAR(20), opsional
            $table->string('profile_picture_url')->nullable(); // VARCHAR(255), opsional
            $table->boolean('is_active')->default(true); // BOOLEAN DEFAULT TRUE
            $table->rememberToken(); // VARCHAR(100) for "remember me"
            $table->timestamps(); // created_at and updated_at TIMESTAMP WITH TIME ZONE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};