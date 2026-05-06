<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __invoke(Request $request)
    {
        $month = CarbonImmutable::parse($request->input('month', now()->format('Y-m-01')))->startOfMonth();
        $workouts = Workout::with('menu')
            ->where('user_id', $request->user()->id)
            ->whereBetween('performed_on', [$month->startOfMonth(), $month->endOfMonth()])
            ->get()
            ->groupBy(fn ($workout) => $workout->performed_on->format('Y-m-d'));

        return view('calendar.index', compact('month', 'workouts'));
    }
}
