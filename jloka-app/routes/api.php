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
});