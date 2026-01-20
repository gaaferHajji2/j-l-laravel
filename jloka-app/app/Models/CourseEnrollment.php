<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $table = 'course_enrollments';
    
    protected $fillable = [
        'student_reg_number',
        'course_code',
        'enrollment_date',
        'grade',
        'status'
    ];
    
    protected $casts = [
        'enrollment_date' => 'date',
    ];

}
