<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\SendGlobalNotificationJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get all categories
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $currentDate = now();

        $sessionCounts = DB::table('sessions')
            ->selectRaw('HOUR(FROM_UNIXTIME(last_activity)) as hour, COUNT(*) as count')
            ->whereBetween('last_activity', [
                $currentDate->copy()->startOfDay()->timestamp,
                $currentDate->copy()->endOfDay()->timestamp,
            ])
            ->groupByRaw('HOUR(FROM_UNIXTIME(last_activity))')
            ->get();

        $result = [];

        $allHoursInDay = collect(range(0, 23))->map(function ($hour) use ($currentDate) {
            return sprintf('%02d', $hour) . ':00' . ($currentDate->hour > $hour ? 'PM' : 'AM');
        });

        $sessionCounts->each(function ($session) use (&$result, $currentDate) {
            $hour = sprintf('%02d', $session->hour) . ':00' . ($currentDate->hour > $session->hour ? 'PM' : 'AM');
            $result[$hour] = $session->count;
        });

        return response()->json([
            'options' => [
                'xaxis' => [
                    'categories' => $allHoursInDay,
                ],
            ],
            'series' => [
                [
                    'name' => 'Sessions',
                    'data' => $allHoursInDay->map(function ($hour) use ($result) {
                        return $result[$hour] ?? 0;
                    })->values()->toArray(),
                ],
            ],
            'total' => DB::table('sessions')
            ->whereBetween('last_activity', [
                $currentDate->now()->subMinutes(5)->timestamp,
                $currentDate->timestamp,
            ])->count(),
            'ok' => true,
        ]);
    }


    /**
     * sendNotification function
     * @param Request $request
     * @return JsonResponse
     */
    public function sendNotification(Request $request): JsonResponse
{
    /**
     * 'role',[
     * 'admin',
     * 'business',
     * 'customer',
     * ]
     */
    $request->validate([
        'title' => 'required|string|max:255', 
        'body'  => 'required|string',
        'role'  => 'required|string|in:all,admin,business,customer',
    ]);

    /**
     * Make job to sent to all users
     * SendGlobalNotificationJob
     */
    $job = new SendGlobalNotificationJob([
        'title' => $request->get('title'), 
        'body'  => $request->get('body'),
        'role'  => $request->get('role'),
    ]);

    dispatch($job)->onQueue('notifications')->delay(now()->addSeconds(5));

    return response()->json([
        'message' => 'Notification sent successfully',
        'ok' => true,
    ]);
}
}
