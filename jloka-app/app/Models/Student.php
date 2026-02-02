<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasUuids;
    protected $primaryKey = 'student_reg_no';
    public $incrementing = false;
    
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 
            'student_reg_number',  // Foreign key on pivot table for student
            'course_code',         // Foreign key on pivot table for course
            'student_reg_no',      // Local key on student table
            'course_identifier'    // Local key on course table
        )->withPivot(['enrollment_date', 'grade', 'status'])
        ->withTimestamps()
        ->using(CourseEnrollment::class); // Custom pivot model
    }
}