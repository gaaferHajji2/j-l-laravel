<?php

use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/serial-task', function () {
    $start = hrtime(true);
    [$fn1, $fn2] = [
        function () {
            sleep(2);
            return 'Hello';
        },
        function () {
            sleep(2);
            return 'World';
        },
    ];
    $result1 = $fn1();
    $result2 = $fn2();
    $end = hrtime(true);
    return "{$result1} {$result2} in " . ($end - $start) / 1000000000 . ' seconds';
});

Route::get('/concurrent-task', function () {
    $start = hrtime(true);
    [$result1, $result2] = Octane::concurrently([
        function () {
            sleep(2);
            return 'Hello';
        },
        function () {
            sleep(2);
            return 'World';
        },
    ]);
    $end = hrtime(true);
    return "{$result1} {$result2} in " . ($end - $start) /
        1000000000 . ' seconds';
});
