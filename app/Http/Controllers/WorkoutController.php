<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\SchedulePattern;
use App\Models\Workout;
use App\Models\WorkoutSet;
use App\Services\TrainingMetrics;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function index(Request $request)
    {
        return view('workouts.index', [
            'workouts' => Workout::with('menu')->where('user_id', $request->user()->id)->latest('performed_on')->paginate(15),
            'menus' => Menu::where('user_id', $request->user()->id)->orderBy('name')->get(),
            'todayMenu' => $this->todayMenu($request),
        ]);
    }

    public function store(Request $request, TrainingMetrics $metrics)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'performed_on' => ['required', 'date'],
        ]);
        $menu = Menu::with('items')->where('user_id', $request->user()->id)->findOrFail($data['menu_id']);
        $workout = Workout::create([
            'user_id' => $request->user()->id,
            'menu_id' => $menu->id,
            'performed_on' => $data['performed_on'],
            'status' => 'completed',
        ]);

        foreach ($menu->items as $item) {
            for ($set = 1; $set <= $item->sets; $set++) {
                $workout->sets()->create([
                    'machine_usage_id' => $item->machine_usage_id,
                    'set_number' => $set,
                    'weight_kg' => $item->weight_kg,
                    'reps' => $item->reps,
                    'estimated_1rm' => $metrics->estimatedOneRepMax((float) $item->weight_kg, (int) $item->reps),
                ]);
            }
        }

        return redirect()->route('workouts.show', $workout)->with('status', 'ワークアウトを開始しました。');
    }

    public function show(Request $request, Workout $workout)
    {
        $this->authorizeOwner($request, $workout);
        $previous = Workout::with('sets')
            ->where('user_id', $request->user()->id)
            ->where('menu_id', $workout->menu_id)
            ->where('id', '<>', $workout->id)
            ->latest('performed_on')
            ->first();

        return view('workouts.show', [
            'workout' => $workout->load('menu', 'sets.usage.machine'),
            'previous' => $previous,
        ]);
    }

    public function update(Request $request, Workout $workout, TrainingMetrics $metrics)
    {
        $this->authorizeOwner($request, $workout);
        $data = $request->validate([
            'notes' => ['nullable', 'string'],
            'sets' => ['array'],
        ]);
        $workout->update(['notes' => $data['notes'] ?? null]);

        foreach ($request->input('sets', []) as $id => $setData) {
            $set = WorkoutSet::where('workout_id', $workout->id)->findOrFail($id);
            $weight = $setData['weight_kg'] ?? null;
            $reps = $setData['reps'] ?? null;
            $set->update([
                'weight_kg' => $weight,
                'reps' => $reps,
                'rpe' => $setData['rpe'] ?? null,
                'rest_seconds' => $setData['rest_seconds'] ?? null,
                'estimated_1rm' => $metrics->estimatedOneRepMax((float) $weight, (int) $reps),
                'notes' => $setData['notes'] ?? null,
            ]);
        }

        return back()->with('status', 'ワークアウトを保存しました。');
    }

    public function destroy(Request $request, Workout $workout)
    {
        $this->authorizeOwner($request, $workout);
        $workout->delete();

        return redirect()->route('workouts.index')->with('status', 'ワークアウトを削除しました。');
    }

    private function todayMenu(Request $request): ?Menu
    {
        $pattern = SchedulePattern::with('days.menu')->where('user_id', $request->user()->id)->where('is_active', true)->first();

        return $pattern?->days->firstWhere('weekday', now()->dayOfWeek)?->menu;
    }

    private function authorizeOwner(Request $request, Workout $workout): void
    {
        abort_unless($workout->user_id === $request->user()->id, 403);
    }
}
