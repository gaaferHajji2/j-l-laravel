<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Cache;
use Laravel\Octane\Exceptions\TaskTimeoutException;
use Laravel\Octane\Facades\Octane;

class DashboardController extends Controller
{
    public function index()
    {
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

    public function indexConcurrent()
    {
        $time = hrtime(true);
        try {
            [$count, $info, $warning, $alert] = Octane::concurrently([
                fn() => Event::count(),
                fn() => Event::ofType('INFO')->get(),
                fn() => Event::ofType('WARNING')->get(),
                fn() => Event::ofType('ALERT')->get(),
            ]);
        } catch (TaskTimeoutException $e) {
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

    public function indexConcurrentCached()
    {
        $time = hrtime(true);
        [$count, $info, $warning, $alert] = Cache::store('octane')->remember(
            key: 'key-event-cache',
            ttl: 20,
            callback: function () {
                return Octane::concurrently([
                    fn() => Event::count(),
                    fn() => Event::ofType('INFO')->get(),
                    fn() => Event::ofType('WARNING')->get(),
                    fn() => Event::ofType('ALERT')->get(),
                ]);
            }
        );

        $time = (hrtime(true) - $time) / 1_000_000;
        return response()->json([
            'count'     => $count,
            'info'      => $info,
            'warning'   => $warning,
            'alert'     => $alert,
            'totalTime' => $time,
        ]);
    }

    public function indexTickCached()
    {
        $time = hrtime(true);
        try {
            $result = Cache::store('octane')->get(
                'cached-result-tick'
            );
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        $time = (hrtime(true) - $time) / 1_000_000;
        $result['time'] = $time;

        return response()->json($result);
    }
}
