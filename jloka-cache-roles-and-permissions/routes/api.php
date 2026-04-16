<?php

use Illuminate\Http\Request;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')-> group(function(){
    Route::post('/permission/user', [PermissionsController::class, 'assignPermissionToUser']);

    Route::post('/permission/user/revoke', [PermissionsController::class, 'revokePermissionFromUser']);

    Route::post('/permission/role', [PermissionsController::class, 'assignPermissionToRole']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Every path has explicit name() and middleware() attributes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Protected routes (require valid Sanctum token)
Route::middleware('auth:sanctum')->group(function () {

    // Auth management
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');

    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout.all');

    // Products (protected + role/permission middleware applied in controller)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
