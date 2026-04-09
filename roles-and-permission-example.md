Below is a **complete senior-level Laravel example** for building a **permissions + roles system** using the most standard package in Laravel:

---

# Recommended Library

Use Spatie package:

spatie/laravel-permission

It is the **industry standard** for:

* Role management
* Permission assignment
* Permission caching
* Middleware authorization
* User-role-permission relationships

---

# 1) Install Library

```bash
composer require spatie/laravel-permission
```

Publish files:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Run migration:

```bash
php artisan migrate
```

---

# 2) Tables Created Automatically

This package creates:

```text
roles
permissions
model_has_roles
model_has_permissions
role_has_permissions
```

---

# 3) Configure User Model

## app/Models/User.php

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];
}
```

---

# 4) Seed Permissions

Create seeder:

```bash
php artisan make:seeder PermissionSeeder
```

---

## database/seeders/PermissionSeeder.php

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            'orders.view',
            'orders.update'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $admin->givePermissionTo($permissions);

        $manager = Role::firstOrCreate([
            'name' => 'manager'
        ]);

        $manager->givePermissionTo([
            'users.view',
            'products.view',
            'orders.view'
        ]);
    }
}
```

---

Run:

```bash
php artisan db:seed --class=PermissionSeeder
```

---

# 5) Assign Role Dynamically to User

Example service:

## app/Services/UserRoleService.php

```php
<?php

namespace App\Services;

use App\Models\User;

class UserRoleService
{
    public function assignRole(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    public function removeRole(User $user, string $role): void
    {
        $user->removeRole($role);
    }
}
```

---

---

# 6) Assign Permission Dynamically to User

```php
$user->givePermissionTo('products.create');
```

Multiple:

```php
$user->givePermissionTo([
    'products.create',
    'products.edit'
]);
```

---

# 7) Revoke Permission Dynamically from User

```php
$user->revokePermissionTo('products.create');
```

---

# 8) Assign Permission to Role Dynamically

```php
$role = Role::findByName('manager');

$role->givePermissionTo('products.edit');
```

---

# 9) Revoke Permission from Role Dynamically

```php
$role->revokePermissionTo('products.edit');
```

---

# 10) Sync Permissions (Best for Admin Panels)

This replaces all permissions safely:

```php
$role->syncPermissions([
    'users.view',
    'products.view'
]);
```

---

# 11) Dynamic Controller Example

## app/Http/Controllers/Admin/PermissionController.php

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function assignPermissionToUser(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $user->givePermissionTo($request->permission);

        return response()->json([
            'message' => 'Permission assigned'
        ]);
    }

    public function revokePermissionFromUser(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        $user->revokePermissionTo($request->permission);

        return response()->json([
            'message' => 'Permission revoked'
        ]);
    }

    public function assignPermissionToRole(Request $request)
    {
        $role = Role::findByName($request->role);

        $role->givePermissionTo($request->permission);

        return response()->json([
            'message' => 'Permission assigned to role'
        ]);
    }
}
```

---

# 12) Routes

## routes/api.php

```php
Route::post('/permissions/user/assign', [PermissionController::class, 'assignPermissionToUser']);
Route::post('/permissions/user/revoke', [PermissionController::class, 'revokePermissionFromUser']);
Route::post('/permissions/role/assign', [PermissionController::class, 'assignPermissionToRole']);
```

---

# 13) Protect Routes by Permission

```php
Route::middleware(['permission:users.view'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

---

# 14) Protect Routes by Role

```php
Route::middleware(['role:admin'])->group(function () {
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
```

---

# 15) Blade Permission Check

```php
@can('products.create')
    <button>Create Product</button>
@endcan
```

---

# 16) Blade Role Check

```php
@role('admin')
    <button>Admin Panel</button>
@endrole
```

---

# 17) Best Senior Pattern: Enum Permissions

## app/Enums/PermissionEnum.php

```php
<?php

namespace App\Enums;

enum PermissionEnum:string
{
    case USERS_VIEW = 'users.view';
    case USERS_CREATE = 'users.create';
    case PRODUCTS_CREATE = 'products.create';
}
```

Usage:

```php
$user->givePermissionTo(PermissionEnum::PRODUCTS_CREATE->value);
```

This prevents typo bugs.

---

# 18) Best Senior Pattern: Central Permission Service

```php
class PermissionService
{
    public function grantUserPermission(User $user, string $permission): void
    {
        if (!$user->hasPermissionTo($permission)) {
            $user->givePermissionTo($permission);
        }
    }
}
```

---

# 19) Important Cache Refresh

After updates:

```bash
php artisan permission:cache-reset
```

---

# 20) Most Important Real Production Advice

Always separate:

```text
Role = high-level business grouping
Permission = exact action
```

Example:

```text
admin
manager
support
```

Permissions:

```text
orders.create
orders.cancel
orders.refund
```

Never create too many roles.

Use:

```text
Few roles + many permissions
```

This scales better.

---

# Recommended Senior Architecture

```text
app/
 ├── Enums/
 ├── Services/
 ├── Actions/
 ├── Policies/
 ├── Http/Controllers/Admin/
```

---

# If you want, next I can generate a **FULL enterprise Laravel permissions architecture** including:

✅ Policies
✅ Gates
✅ Middleware
✅ Multi-tenant roles
✅ Super Admin
✅ Team permissions
✅ API auth with Laravel Sanctum
✅ clean service-repository pattern

That version is how senior Laravel teams build it in large projects.
