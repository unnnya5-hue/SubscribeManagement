<?php

namespace App\Http\Controllers;

use App\Models\BodyRecord;
use App\Models\MachineUsage;
use App\Models\WorkoutSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function index(Request $request)
    {
        return view('charts.index', [
            'usages' => MachineUsage::whereHas('machine', fn ($query) => $query->where('user_id', $request->user()->id))->orderBy('name')->get(),
        ]);
    }

    public function body(Request $request)
    {
        $records = BodyRecord::where('user_id', $request->user()->id)->where('recorded_date', '>=', now()->subDays(90))->orderBy('recorded_date')->get();

        return response()->json([
            'labels' => $records->map(fn ($record) => $record->recorded_date->format('m/d')),
            'weight' => $records->pluck('weight_kg'),
            'bodyFat' => $records->pluck('body_fat_pct'),
        ]);
    }

    public function progress(Request $request)
    {
        $usageId = $request->integer('usage_id');
        $sets = WorkoutSet::query()
            ->select('workout_sets.*')
            ->join('workouts', 'workouts.id', '=', 'workout_sets.workout_id')
            ->where('workouts.user_id', $request->user()->id)
            ->when($usageId, fn ($query) => $query->where('machine_usage_id', $usageId))
            ->orderBy('workouts.performed_on')
            ->limit(120)
            ->get();

        return response()->json([
            'labels' => $sets->map(fn ($set) => $set->created_at->format('m/d')),
            'oneRm' => $sets->pluck('estimated_1rm'),
            'weight' => $sets->pluck('weight_kg'),
            'volume' => $sets->map(fn ($set) => (float) $set->weight_kg * (int) $set->reps),
        ]);
    }

    public function bodyParts(Request $request)
    {
        $rows = WorkoutSet::query()
            ->join('workouts', 'workouts.id', '=', 'workout_sets.workout_id')
            ->join('machine_usages', 'machine_usages.id', '=', 'workout_sets.machine_usage_id')
            ->where('workouts.user_id', $request->user()->id)
            ->where('workouts.performed_on', '>=', now()->subDays(30))
            ->groupBy('machine_usages.body_part')
            ->select('machine_usages.body_part', DB::raw('SUM(COALESCE(weight_kg, 0) * COALESCE(reps, 0)) as volume'))
            ->pluck('volume', 'body_part');

        return response()->json([
            'labels' => $rows->keys()->map(fn ($label) => $label ?: '未設定'),
            'volume' => $rows->values(),
        ]);
    }
}
