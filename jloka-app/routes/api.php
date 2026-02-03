<?php

use App\Http\Controllers\API\FirstController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('customers')->group(function() {
    Route::get('/', [FirstController::class, 'getAllCustomers']);
    Route::post('/', [FirstController::class, 'createNewCustomer']);
    Route::get('/{id}', [FirstController::class, 'getCustomerById']);
});

Route::prefix('passports')->group(function() {
    Route::post('/', [FirstController::class, 'createNewPassportData']);
    Route::get('/{id}', [FirstController::class, 'getPassportWithCustomerById']);
});

Route::prefix('authors')->group(function() {
    Route::post('/', [FirstController::class, 'createNewAuthor']);
    Route::get('/{id}', [FirstController::class, 'getAuthorById']);
});

Route::prefix('books')->group(function() {
    Route::post('/', [FirstController::class, 'createNewBook']);
    Route::get('/{id}', [FirstController::class, 'getBookById']);
});