<?php

namespace App\Http\Controllers;

use App\Models\BodyRecord;
use App\Models\SchedulePattern;
use App\Models\Workout;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = $request->user()->id;
        $latestBody = BodyRecord::where('user_id', $userId)->latest('recorded_date')->first();
        $thirtyDaysAgo = BodyRecord::where('user_id', $userId)->where('recorded_date', '<=', now()->subDays(30))->latest('recorded_date')->first();
        $recentWorkouts = Workout::with('menu')->where('user_id', $userId)->latest('performed_on')->limit(5)->get();
        $activePattern = SchedulePattern::with('days.menu')->where('user_id', $userId)->where('is_active', true)->first();
        $weekStart = CarbonImmutable::now()->startOfWeek();
        $week = collect(range(0, 6))->map(function ($offset) use ($weekStart, $activePattern) {
            $date = $weekStart->addDays($offset);
            $day = $activePattern?->days->firstWhere('weekday', $date->dayOfWeek);

            return ['date' => $date, 'menu' => $day?->menu];
        });

        return view('dashboard.index', [
            'latestBody' => $latestBody,
            'bodyDelta' => $latestBody && $thirtyDaysAgo ? round($latestBody->weight_kg - $thirtyDaysAgo->weight_kg, 1) : null,
            'recentWorkouts' => $recentWorkouts,
            'week' => $week,
            'workoutCount' => Workout::where('user_id', $userId)->count(),
        ]);
    }
}
