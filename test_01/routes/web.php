<?php

use App\Services\DeployApp;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/img', function() {

    $url = Storage::temporaryUrl('Sword_02.jpg', now()->addSeconds(10));

    return response()->json([
        'url' => $url
    ]);

});

Route::get('/demo', function(DeployApp $deploy) {

    return response()->json([
        "status" => "Deploy Ok",
    ], 200);

    // dd($deploy);
});