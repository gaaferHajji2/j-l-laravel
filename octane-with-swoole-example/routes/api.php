<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
// use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;
use OpenSwoole\Coroutine\Http\Client;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Octane::route('GET', '/dashboard', function() {
    return (new DashboardController)->index();
});

Octane::route('GET', '/dashboard-concurrent', function() {
    return (new DashboardController)->indexConcurrent();
});

Octane::route('GET', '/api/sentence', function () {
    return response()->json([
        'text' => fake()->sentence()
    ]);
});
Octane::route('GET', '/api/name', function () {
    return response()->json([
        'name' => fake()->name()
    ]);
});

Octane::route('GET', '/httpcall/sequence', function () {
    $time           = hrtime(true);
    $sentenceJson   = Http::get('http://127.0.0.1:8000/api/sentence')->json();
    $nameJson       = Http::get('http://127.0.0.1:8000/api/name')->json();
    $time           = hrtime(true) - $time;
    
    return response()->json(
        array_merge(
            $sentenceJson,
            $nameJson,
            ["time_ms" => $time / 1_000_000]
        )
    );
});

// Octane::route('GET', '/httpcall/parallel', function () {
//     $time = hrtime(true);
//     [$sentenceJson, $nameJson] = Octane::concurrently([
//         fn() => Http::get('http://127.0.0.1:8000/api/sentence')->json(),
//         fn() => Http::get('http://127.0.0.1:8000/api/sequence')->json()
//     ]);
//     $time = hrtime(true) - $time;
//     error_log(json_encode($sentenceJson));
//     error_log(json_encode($nameJson));
//     return response()->json(
//         array_merge(
//             $sentenceJson,
//             $nameJson,
//         )
//     );
// });

function makeSwooleRequest(string $path): array
{
    $client = new Client('127.0.0.1', 8000);
    $client->set(['timeout' => 5]);
    $client->get($path);
    
    if ($client->errCode !== 0) {
        throw new \Exception("Request failed: " . $client->errMsg);
    }
    
    $body = $client->body;
    $client->close();
    
    return json_decode($body, true);
}