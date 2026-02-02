<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasUuids;
    protected $primaryKey = 'course_identifier';
    public $incrementing = false;
    
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_enrollments',
            'course_code',         // Foreign key on pivot table for course
            'student_reg_number',  // Foreign key on pivot table for student
            'course_identifier',   // Local key on course table
            'student_reg_no'       // Local key on student table
        )->withPivot(['enrollment_date', 'grade', 'status'])
        ->withTimestamps()
        ->using(CourseEnrollment::class);
    }

}
