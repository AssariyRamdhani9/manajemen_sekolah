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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_subject_teacher_id')->constrained('class_subject_teachers')->onDelete('cascade');
            $table->string('semester', 20)->default('1');
            $table->string('academic_year', 20)->default('2025/2026');
            $table->decimal('assignment_score', 5, 2)->default(0);
            $table->decimal('uts_score', 5, 2)->default(0);
            $table->decimal('uas_score', 5, 2)->default(0);
            $table->decimal('final_score', 5, 2)->default(0);
            $table->string('letter_grade', 5)->default('D');
            $table->text('teacher_notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'class_subject_teacher_id', 'semester', 'academic_year'], 'unique_student_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
