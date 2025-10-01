<?php

use App\Services\DeployApp;
use App\Services\GetUser;
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

Route::get('/user', function(GetUser $getUserInfo) {

    return response()->json([
        'msg' => 'Getting user',
    ]);

});