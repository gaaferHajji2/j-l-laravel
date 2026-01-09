I'll create a comprehensive example with all Laravel relationship types and custom configurations.

## 1. Database Schema (Migration Files)

```php
// database/migrations/create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamps();
});

// database/migrations/create_profiles_table.php
Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('bio');
    $table->string('phone');
    $table->timestamps();
});

// database/migrations/create_posts_table.php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
    $table->string('title');
    $table->text('content');
    $table->timestamps();
});

// database/migrations/create_comments_table.php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->onDelete('cascade');
    $table->foreignId('commenter_id')->constrained('users')->onDelete('cascade');
    $table->text('body');
    $table->timestamps();
});

// database/migrations/create_tags_table.php
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// database/migrations/create_post_tag_table.php (Pivot table)
Schema::create('post_tag', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->onDelete('cascade');
    $table->foreignId('tag_id')->constrained()->onDelete('cascade');
    $table->integer('priority')->default(0);
    $table->timestamp('attached_at')->useCurrent();
    $table->unique(['post_id', 'tag_id']);
});

// database/migrations/create_roles_table.php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// database/migrations/create_role_user_table.php (Polymorphic pivot)
Schema::create('role_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('role_id')->constrained()->onDelete('cascade');
    $table->morphs('roleable'); // Creates roleable_id and roleable_type
    $table->timestamps();
});

// database/migrations/create_images_table.php (Polymorphic)
Schema::create('images', function (Blueprint $table) {
    $table->id();
    $table->string('url');
    $table->morphs('imageable'); // Creates imageable_id and imageable_type
    $table->timestamps();
});

// database/migrations/create_countries_table.php
Schema::create('countries', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});

// database/migrations/create_locations_table.php (Has One Through)
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('country_id')->constrained()->onDelete('cascade');
    $table->string('address');
    $table->timestamps();
});
```

## 2. Models with All Relationship Types

```php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Model
{
    // 1. ONE TO ONE (with custom foreign key)
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id'); // 'user_id' is custom foreign key
    }
    
    // 2. ONE TO MANY (with custom foreign key)
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id'); // Custom foreign key
    }
    
    // 3. MANY TO MANY (with custom pivot table)
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user') // Custom pivot table
                    ->withPivot('created_at') // Extra pivot columns
                    ->wherePivot('roleable_type', self::class) // Constrain by type
                    ->withTimestamps();
    }
    
    // 4. HAS ONE THROUGH
    public function locationCountry(): HasOneThrough
    {
        return $this->hasOneThrough(
            Country::class,      // Final model
            Location::class,     // Intermediate model
            'user_id',          // Foreign key on intermediate table
            'id',               // Foreign key on final table
            'id',               // Local key
            'country_id'        // Local key on intermediate table
        );
    }
    
    // 5. HAS MANY THROUGH
    public function commentsOnPosts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Comment::class,     // Final model
            Post::class,        // Intermediate model
            'author_id',        // Foreign key on intermediate table
            'post_id',          // Foreign key on final table
            'id',               // Local key
            'id'                // Local key on intermediate table
        );
    }
    
    // 6. POLYMORPHIC MANY TO MANY
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    
    // 7. POLYMORPHIC ONE TO MANY
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    
    // Inverse of Comment relationship
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'commenter_id');
    }
}
```

```php
// app/Models/Profile.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    // ONE TO ONE (Inverse)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); // Custom foreign key
    }
}
```

```php
// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Post extends Model
{
    // ONE TO MANY (Inverse with custom foreign key)
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id'); // Custom foreign key
    }
    
    // ONE TO MANY
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    // MANY TO MANY (with custom pivot table configuration)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag') // Custom pivot table name
                    ->using(PostTag::class) // Custom pivot model
                    ->withPivot(['priority', 'attached_at']) // Extra pivot columns
                    ->withTimestamps()
                    ->orderByPivot('priority', 'desc'); // Order by pivot column
    }
    
    // POLYMORPHIC ONE TO MANY
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    
    // POLYMORPHIC MANY TO MANY
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'roleable', 'role_user');
    }
}
```

```php
// app/Models/Comment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
    
    public function commenter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commenter_id'); // Custom foreign key
    }
}
```

```php
// app/Models/Tag.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    // MANY TO MANY (Inverse)
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag') // Custom pivot table
                    ->withPivot(['priority', 'attached_at'])
                    ->withTimestamps();
    }
    
    // POLYMORPHIC MANY TO MANY (Inverse)
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }
    
    public function postsAsTaggable(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
}
```

```php
// app/Models/Role.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends Model
{
    // POLYMORPHIC MANY TO MANY (Inverse)
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'roleable', 'role_user');
    }
    
    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'roleable', 'role_user');
    }
}
```

```php
// app/Models/Image.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    // POLYMORPHIC ONE TO MANY (Inverse)
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
```

```php
// app/Models/Country.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    // HAS MANY THROUGH (Inverse)
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            Location::class,
            'country_id',   // Foreign key on intermediate table
            'id',           // Foreign key on final table
            'id',           // Local key
            'user_id'       // Local key on intermediate table
        );
    }
}
```

```php
// app/Models/Location.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
```

```php
// app/Models/PostTag.php (Custom Pivot Model)
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostTag extends Pivot
{
    protected $table = 'post_tag';
    
    // Cast pivot columns
    protected $casts = [
        'attached_at' => 'datetime',
        'priority' => 'integer',
    ];
    
    // Pivot model methods
    public function markAsHighPriority()
    {
        $this->priority = 10;
        $this->save();
    }
}
```

## 3. Usage Examples in Controller

```php
// app/Http/Controllers/ExampleController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Tag;

class ExampleController extends Controller
{
    public function oneToOne()
    {
        $user = User::find(1);
        
        // Get profile with custom foreign key
        $profile = $user->profile; // Uses 'user_id' foreign key
        
        // Create profile
        $user->profile()->create([
            'bio' => 'Developer',
            'phone' => '1234567890'
        ]);
        
        // Eager load
        $users = User::with('profile')->get();
    }
    
    public function oneToMany()
    {
        $user = User::find(1);
        
        // Get posts with custom foreign key 'author_id'
        $posts = $user->posts;
        
        // Create post
        $user->posts()->create([
            'title' => 'New Post',
            'content' => 'Content here'
        ]);
        
        // With custom foreign key constraint
        $posts = Post::where('author_id', $user->id)->get();
    }
    
    public function manyToMany()
    {
        $post = Post::find(1);
        $tag = Tag::find(1);
        
        // Attach with custom pivot data
        $post->tags()->attach($tag->id, [
            'priority' => 5,
            'attached_at' => now()
        ]);
        
        // Sync with pivot data
        $post->tags()->sync([
            1 => ['priority' => 3],
            2 => ['priority' => 1]
        ]);
        
        // Detach
        $post->tags()->detach($tag->id);
        
        // Access pivot data
        foreach ($post->tags as $tag) {
            echo $tag->pivot->priority;
            echo $tag->pivot->attached_at;
        }
        
        // Query pivot table
        $highPriorityTags = $post->tags()
            ->wherePivot('priority', '>', 5)
            ->get();
            
        // Using custom pivot model methods
        $pivot = $post->tags()->where('tag_id', 1)->first()->pivot;
        $pivot->markAsHighPriority();
    }
    
    public function hasOneThrough()
    {
        $user = User::find(1);
        
        // Get country through location
        $country = $user->locationCountry;
        
        // Eager load
        $users = User::with('locationCountry')->get();
    }
    
    public function hasManyThrough()
    {
        $user = User::find(1);
        
        // Get all comments on user's posts
        $comments = $user->commentsOnPosts;
        
        // With eager loading
        $users = User::with('commentsOnPosts')->get();
    }
    
    public function polymorphic()
    {
        $user = User::find(1);
        $post = Post::find(1);
        
        // Store images for different models
        $user->images()->create([
            'url' => 'user-avatar.jpg'
        ]);
        
        $post->images()->create([
            'url' => 'post-image.jpg'
        ]);
        
        // Get polymorphic images
        $userImages = $user->images;
        $postImages = $post->images;
        
        // Polymorphic many-to-many (roles)
        $user->roles()->attach(1);
        $post->roles()->attach([1, 2]);
        
        // Get roles for any model
        $userRoles = $user->roles;
        $postRoles = $post->roles;
        
        // Get all users with a specific role
        $role = Role::find(1);
        $usersWithRole = $role->users;
        $postsWithRole = $role->posts;
    }
    
    public function advancedQueries()
    {
        // Query with custom foreign key
        $posts = Post::whereHas('author', function ($query) {
            $query->where('author_id', 1); // Using custom foreign key
        })->get();
        
        // Eager load with constraints
        $users = User::with(['posts' => function ($query) {
            $query->where('title', 'like', '%Laravel%');
        }])->get();
        
        // Access pivot table directly
        $postTags = \DB::table('post_tag')
            ->where('priority', '>', 3)
            ->get();
            
        // Multiple relationships
        $user = User::with([
            'profile',
            'posts.tags',
            'commentsOnPosts',
            'images',
            'roles'
        ])->find(1);
    }
    
    public function createWithRelationships()
    {
        // Create user with related models
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        // Create profile (one-to-one)
        $profile = $user->profile()->create([
            'bio' => 'Web Developer',
            'phone' => '555-1234'
        ]);
        
        // Create posts (one-to-many)
        $post = $user->posts()->create([
            'title' => 'First Post',
            'content' => 'Content here'
        ]);
        
        // Attach tags (many-to-many with pivot data)
        $post->tags()->attach([
            1 => ['priority' => 2],
            2 => ['priority' => 1]
        ]);
        
        // Create image (polymorphic)
        $user->images()->create([
            'url' => 'avatar.jpg'
        ]);
        
        // Attach roles (polymorphic many-to-many)
        $user->roles()->sync([1, 3]);
    }
}
```

## 4. Factory Examples

```php
// database/factories/UserFactory.php
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
    
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Create related models
            Profile::factory()->create(['user_id' => $user->id]);
            
            // Create posts with tags
            $posts = Post::factory(3)->create(['author_id' => $user->id]);
            
            foreach ($posts as $post) {
                $tags = Tag::factory(2)->create();
                $post->tags()->attach($tags, [
                    'priority' => rand(1, 10),
                    'attached_at' => now()
                ]);
            }
        });
    }
}
```

This comprehensive example demonstrates:

1. **All relationship types**: One-to-One, One-to-Many, Many-to-Many, Has-One-Through, Has-Many-Through, Polymorphic One-to-Many, Polymorphic Many-to-Many
2. **Custom foreign keys**: Specifying custom foreign key names (e.g., `author_id`, `commenter_id`)
3. **Custom pivot tables**: Using custom table names and pivot models
4. **Pivot table data**: Storing extra data in pivot tables with timestamps
5. **Query methods**: Using `withPivot()`, `wherePivot()`, `orderByPivot()`
6. **Eager loading**: Loading nested relationships efficiently
7. **Polymorphic relationships**: Sharing relationships between different models

Key custom configurations shown:
- Custom foreign key names in relationships
- Custom pivot table names
- Extra pivot columns with casting
- Custom pivot model with methods
- Polymorphic relationships with type constraints
- Advanced query constraints on relationships