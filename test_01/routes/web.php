<?php

use App\Services\DeployApp;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/demo', function(DeployApp $deploy) {

    // return response()->json([
    //     "status" => "Deploy Ok",
    // ], 200);

    // dd($deploy);
});