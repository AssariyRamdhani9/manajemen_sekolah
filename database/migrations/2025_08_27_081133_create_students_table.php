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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade'); // Foreign key to users
            $table->string('nis', 50)->unique();
            $table->foreignId('class_id')->constrained('classes')->onDelete('restrict'); // Foreign key to classes
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('parent_phone_number', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
