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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('student_reg_number'); // Custom foreign key for student
            $table->string('course_code');        // Custom foreign key for course
            
            // Custom columns in pivot table
            $table->date('enrollment_date');
            $table->string('grade')->nullable();
            $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
            
            // Custom foreign key constraints
            $table->foreign('student_reg_number')
                  ->references('student_reg_no')
                  ->on('students')
                  ->onDelete('cascade');
                  
            $table->foreign('course_code')
                  ->references('course_identifier')
                  ->on('courses')
                  ->onDelete('cascade');
            
            // Composite unique key
            $table->unique(['student_reg_number', 'course_code']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
