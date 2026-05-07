<?php
use App\MyClass;
use Illuminate\Support\Facades\Route;

$myStartTime = microtime(true);
Route::get('/', function () use($myStartTime) {
    $myLocalStartTime = microtime(true);
    return DateTime::createFromFormat('U.u', $myStartTime)->format("r (u)") . 
    " - " . 
    DateTime::createFromFormat('U.u', $myLocalStartTime)->format("r (u)");
});

Route::get('/static-class', function (MyClass $myclass) {
    $myclass->add();
    return $myclass->get();
});
