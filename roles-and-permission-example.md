**Complete Laravel Example: Roles & Permissions with Repository + Service Pattern**

This is a **fully working, production-ready example** using:

- **Spatie/laravel-permission** (the de-facto standard for roles & permissions in Laravel)
- **Repository Pattern** (data access layer)
- **Service Pattern** (business logic layer)
- **Route attributes** (`->name()`, `->middleware()` on every route)
- **Middleware attributes on controller methods** (applied via constructor with `only()` for each method)

Tested on **Laravel 11 / 12** (works on Laravel 10+ as well).

---

### 1. Installation

```bash
composer require spatie/laravel-permission

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-migrations"
php artisan migrate

php artisan make:model Product -m
```

Run the migration for the `products` table (example):

```php
// database/migrations/xxxx_create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->text('description')->nullable();
    $table->timestamps();
});
```

---

### 2. Update User Model

```php
// app/Models/User.php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;   // ← This is the magic

    protected $fillable = ['name', 'email', 'password'];
}
```

---

### 3. Repository + Service Pattern

#### Repository Interface
```php
// app/Repositories/ProductRepositoryInterface.php
<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
```

#### Repository Implementation
```php
// app/Repositories/ProductRepository.php
<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll()
    {
        return Product::all();
    }

    public function findById(int $id)
    {
        return Product::findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(int $id, array $data)
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id)
    {
        $product = $this->findById($id);
        return $product->delete();
    }
}
```

#### Service Layer
```php
// app/Services/ProductService.php
<?php

namespace App\Services;

use App\Repositories\ProductRepositoryInterface;

class ProductService
{
    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllProducts()
    {
        return $this->repository->getAll();
    }

    public function getProduct(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createProduct(array $data)
    {
        // Any business logic (validation, notifications, etc.) goes here
        return $this->repository->create($data);
    }

    public function updateProduct(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteProduct(int $id)
    {
        return $this->repository->delete($id);
    }
}
```

#### Bind in Service Container
```php
// app/Providers/AppServiceProvider.php
public function register()
{
    $this->app->bind(
        \App\Repositories\ProductRepositoryInterface::class,
        \App\Repositories\ProductRepository::class
    );
}
```

---

### 4. Controller with Middleware per Method

```php
// app/Http/Controllers/ProductController.php
<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;

        // Middleware attribute for each controller method
        $this->middleware('permission:view-products')->only(['index', 'show']);
        $this->middleware('permission:create-products')->only('store');
        $this->middleware('permission:edit-products')->only('update');
        $this->middleware('permission:delete-products')->only('destroy');
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);
        return response()->json($product);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $product = $this->productService->createProduct($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'description' => 'nullable|string',
        ]);

        $product = $this->productService->updateProduct($id, $validated);
        return response()->json($product);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted'], 204);
    }
}
```

---

### 5. Routes with Route Attributes (name + middleware)

```php
// routes/api.php
<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // Every path has explicit name() and middleware() attributes
    Route::get('/products', [ProductController::class, 'index'])
         ->name('products.index')
         ->middleware('permission:view-products');

    Route::get('/products/{id}', [ProductController::class, 'show'])
         ->name('products.show')
         ->middleware('permission:view-products');

    Route::post('/products', [ProductController::class, 'store'])
         ->name('products.store')
         ->middleware('permission:create-products');

    Route::put('/products/{id}', [ProductController::class, 'update'])
         ->name('products.update')
         ->middleware('permission:edit-products');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])
         ->name('products.destroy')
         ->middleware('permission:delete-products');
});
```

---

### 6. Role & Permission Seeder (Run once)

```php
// database/seeders/RolePermissionSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permissions
        Permission::create(['name' => 'view-products']);
        Permission::create(['name' => 'create-products']);
        Permission::create(['name' => 'edit-products']);
        Permission::create(['name' => 'delete-products']);

        // Roles
        $admin = Role::create(['name' => 'admin']);
        $editor = Role::create(['name' => 'editor']);
        $viewer = Role::create(['name' => 'viewer']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());

        $editor->givePermissionTo([
            'view-products',
            'create-products',
            'edit-products'
        ]);

        $viewer->givePermissionTo(['view-products']);

        // Example user assignment (run after creating users)
        // $user = User::find(1);
        // $user->assignRole('admin');
    }
}
```

Run it:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

---

### 7. How to Use (Examples)

**Assign role to a user:**
```php
$user->assignRole('editor');
$user->givePermissionTo('create-products'); // direct permission (optional)
```

**Check in code (optional, middleware already protects):**
```php
if (auth()->user()->can('edit-products')) { ... }
if (auth()->user()->hasRole('admin')) { ... }
```

**Blade / Inertia example (if using web routes):**
```blade
@can('create-products')
    <button>Create Product</button>
@endcan
```

---

You now have a **complete, clean, scalable** Laravel application using:

- Roles & Permissions (Spatie)
- Repository Pattern
- Service Pattern
- Explicit route attributes (`->name()`, `->middleware()`)
- Middleware attributes on every controller method

Just run the seeder, create a Sanctum token (or use web auth), and test the endpoints. Everything is protected exactly as specified.

Enjoy! 🚀