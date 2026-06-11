<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Laravel\Octane\Exceptions\TaskTimeoutException;
use Laravel\Octane\Facades\Octane;

class DashboardController extends Controller
{
    public function index() {
        $time = hrtime(true);
        $count = Event::count();
        $info = Event::ofType('INFO')->get();
        $warning = Event::ofType('WARNING')->get();
        $alert = Event::ofType('ALERT')->get();
        $totalTime = (hrtime(true) - $time) / 1_000_000;

        return response()->json([
            'count'     => $count,
            'info'      => $info,
            'warning'   => $warning,
            'alert'     => $alert,
            'totalTime' => $totalTime,
        ]);
    }

    public function indexConcurrent() {
        $time = hrtime(true);
        try {
            [$count, $info, $warning, $alert] = Octane::concurrently([
                fn () => Event::count(),
                fn () => Event::ofType('INFO')->get(),
                fn () => Event::ofType('WARNING')->get(),
                fn () => Event::ofType('ALERT')->get(),
            ]);

        }catch (TaskTimeoutException $e) {
            return response()->json([
                "msg" => $e->getMessage(),
            ]);
        }
        $totalTime = (hrtime(true) - $time) / 1_000_000;
        return response()->json([
            'count'     => $count,
            'info'      => $info,
            'warning'   => $warning,
            'alert'     => $alert,
            'totalTime' => $totalTime,
        ]);
    }
}
