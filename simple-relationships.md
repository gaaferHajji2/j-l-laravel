Here's a comprehensive example with custom foreign keys, custom pivot table names, and custom data in the pivot table:

## Models and Relationships

### 1. One-to-One Relationship: `User` ↔ `Passport`

**User Model:**
```php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function passport()
    {
        return $this->hasOne(Passport::class, 'user_identifier', 'user_code');
    }
}
```

**Passport Model:**
```php
// app/Models/Passport.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passport extends Model
{
    protected $primaryKey = 'passport_uid';
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_identifier', 'user_code');
    }
}
```

### 2. One-to-Many / Many-to-One: `Author` ↔ `Book`

**Author Model:**
```php
// app/Models/Author.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $primaryKey = 'author_slug';
    public $incrementing = false;
    
    public function books()
    {
        return $this->hasMany(Book::class, 'writer_id', 'author_slug');
    }
}
```

**Book Model:**
```php
// app/Models/Book.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public function author()
    {
        return $this->belongsTo(Author::class, 'writer_id', 'author_slug');
    }
}
```

### 3. Many-to-Many: `Student` ↔ `Course` (with custom pivot)

**Student Model:**
```php
// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
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
```

**Course Model:**
```php
// app/Models/Course.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
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
```

**Custom Pivot Model:**
```php
// app/Models/CourseEnrollment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseEnrollment extends Pivot
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
```

## Migration Files

### 1. Users Table Migration
```php
// database/migrations/xxxx_xx_xx_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_code')->unique(); // Custom primary key reference
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

### 2. Passports Table Migration (One-to-One)
```php
// database/migrations/xxxx_xx_xx_create_passports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassportsTable extends Migration
{
    public function up()
    {
        Schema::create('passports', function (Blueprint $table) {
            $table->id('passport_uid'); // Custom primary key name
            $table->string('passport_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('country');
            $table->string('user_identifier'); // Custom foreign key
            
            // Custom foreign key constraint
            $table->foreign('user_identifier')
                  ->references('user_code')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('passports');
    }
}
```

### 3. Authors Table Migration
```php
// database/migrations/xxxx_xx_xx_create_authors_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->string('author_slug')->primary(); // Custom primary key
            $table->string('name');
            $table->string('email')->unique();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('authors');
    }
}
```

### 4. Books Table Migration (One-to-Many/Many-to-One)
```php
// database/migrations/xxxx_xx_xx_create_books_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique();
            $table->date('published_date');
            $table->string('writer_id'); // Custom foreign key
            
            // Custom foreign key constraint
            $table->foreign('writer_id')
                  ->references('author_slug')
                  ->on('authors')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
}
```

### 5. Students Table Migration
```php
// database/migrations/xxxx_xx_xx_create_students_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('student_reg_no')->primary(); // Custom primary key
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
```

### 6. Courses Table Migration
```php
// database/migrations/xxxx_xx_xx_create_courses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->string('course_identifier')->primary(); // Custom primary key
            $table->string('course_name');
            $table->string('course_code')->unique();
            $table->integer('credits');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
```

### 7. Custom Pivot Table Migration (Many-to-Many)
```php
// database/migrations/xxxx_xx_xx_create_course_enrollments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseEnrollmentsTable extends Migration
{
    public function up()
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

    public function down()
    {
        Schema::dropIfExists('course_enrollments');
    }
}
```

## Usage Examples

```php
// One-to-One Usage
$user = User::where('user_code', 'USER001')->first();
$passport = $user->passport; // Get passport
$passportOwner = $passport->user; // Get user from passport

// One-to-Many Usage
$author = Author::where('author_slug', 'jk-rowling')->first();
$books = $author->books; // Get all books by author
$book = Book::find(1);
$bookAuthor = $book->author; // Get author of a book

// Many-to-Many Usage
$student = Student::where('student_reg_no', 'STU2024001')->first();
// Enroll student in course with pivot data
$student->courses()->attach('CS101', [
    'enrollment_date' => now(),
    'grade' => null,
    'status' => 'active'
]);

// Get courses with pivot data
$courses = $student->courses()->withPivot(['enrollment_date', 'grade'])->get();

// Get students in a course
$course = Course::where('course_identifier', 'CS101')->first();
$students = $course->students()->wherePivot('status', 'active')->get();

// Update pivot data
$student->courses()->updateExistingPivot('CS101', [
    'grade' => 'A',
    'status' => 'completed'
]);
```

## Key Customizations in This Example:

1. **Custom Primary Keys**: `user_code`, `author_slug`, `student_reg_no`, `course_identifier`
2. **Custom Foreign Keys**: `user_identifier`, `writer_id`
3. **Custom Pivot Table Name**: `course_enrollments` (instead of `course_student`)
4. **Custom Foreign Keys in Pivot**: `student_reg_number`, `course_code`
5. **Custom Pivot Data**: `enrollment_date`, `grade`, `status`
6. **Custom Pivot Model**: `CourseEnrollment` with additional functionality
7. **Custom Column Names**: All foreign keys have custom names that don't follow Laravel's conventions