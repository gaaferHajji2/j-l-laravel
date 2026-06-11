<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Octane::route('GET', '/dashboard', function() {
    return (new DashboardController)->index();
});

Octane::route('GET', '/dashboard-concurrent', function() {
    return (new DashboardController)->indexConcurrent();
});