<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
// use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;
use Laravel\Octane\Swoole\SwooleClient\Coroutine\Http\Client;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Octane::route('GET', '/dashboard', function () {
    return (new DashboardController)->index();
});

Octane::route('GET', '/dashboard-concurrent', function () {
    return (new DashboardController)->indexConcurrent();
});

Octane::route('GET', '/api/sentence', function () {
    sleep(1);
    return response()->json([
        'text' => fake()->sentence()
    ]);
});
Octane::route('GET', '/api/name', function () {
    sleep(1);
    return response()->json([
        'name' => fake()->name()
    ]);
});

Route::get('/httpcall/sequence', function () {
    $time = hrtime(true);
    $sentenceJson = Http::get('http://127.0.0.1:8000/api/sentence')->json() ?? [];
    $nameJson = Http::get('http://127.0.0.1:8000/api/name')->json() ?? [];
    $time = hrtime(true) - $time;

    return response()->json(
        array_merge(
            $sentenceJson,
            $nameJson,
            ['time_ms' => $time / 1_000_000]
        )
    );
});

Route::get('/httpcall/parallel', function () {
    $time = hrtime(true);
    [$sentenceJson, $nameJson] =Cache::store('octane')->remember(
        'key-checking',20, function () {
                return Octane::concurrently([
                fn() => Http::get('http://127.0.0.1:8000/api/sentence'
                    )->json(),

                fn() => Http::get('http://127.0.0.1:8000/api/name')->json(),
            ]);
        }
    );
    $time = hrtime(true) - $time;

    return response()->json(
        array_merge(
            $sentenceJson,
            $nameJson,
            ['time_ms' => $time / 1_000_000]
        )
    );
});

Octane::route('GET', '/dashboard-concurrent-cached', function 
() {
    return (new DashboardController)->indexConcurrentCached();
});