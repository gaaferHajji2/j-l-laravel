<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // Every path has explicit name() and middleware() attributes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
