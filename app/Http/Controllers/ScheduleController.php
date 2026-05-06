<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\SchedulePattern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        return view('schedules.index', [
            'patterns' => SchedulePattern::with('days.menu')->where('user_id', $request->user()->id)->latest()->get(),
            'active' => SchedulePattern::with('days.menu')->where('user_id', $request->user()->id)->where('is_active', true)->first(),
        ]);
    }

    public function create(Request $request)
    {
        return view('schedules.form', $this->formData($request, new SchedulePattern(['repeat_type' => 'weekly'])));
    }

    public function store(Request $request)
    {
        $pattern = SchedulePattern::create($this->validated($request) + ['user_id' => $request->user()->id]);
        $this->syncDays($request, $pattern);

        return redirect()->route('schedules.index')->with('status', 'スケジュールを作成しました。');
    }

    public function edit(Request $request, SchedulePattern $schedule)
    {
        $this->authorizeOwner($request, $schedule);

        return view('schedules.form', $this->formData($request, $schedule->load('days')));
    }

    public function update(Request $request, SchedulePattern $schedule)
    {
        $this->authorizeOwner($request, $schedule);
        $schedule->update($this->validated($request));
        $this->syncDays($request, $schedule);

        return redirect()->route('schedules.index')->with('status', 'スケジュールを更新しました。');
    }

    public function activate(Request $request, SchedulePattern $schedule)
    {
        $this->authorizeOwner($request, $schedule);
        DB::transaction(function () use ($request, $schedule) {
            SchedulePattern::where('user_id', $request->user()->id)->update(['is_active' => false]);
            $schedule->update(['is_active' => true]);
        });

        return back()->with('status', '有効スケジュールを切り替えました。');
    }

    public function destroy(Request $request, SchedulePattern $schedule)
    {
        $this->authorizeOwner($request, $schedule);
        $schedule->delete();

        return back()->with('status', 'スケジュールを削除しました。');
    }

    private function formData(Request $request, SchedulePattern $pattern): array
    {
        return [
            'schedule' => $pattern,
            'menus' => Menu::where('user_id', $request->user()->id)->orderBy('name')->get(),
            'weekdays' => $this->weekdays(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'repeat_type' => ['required', 'in:weekly,biweekly_a,biweekly_b'],
            'starts_on' => ['nullable', 'date'],
        ]);
    }

    private function syncDays(Request $request, SchedulePattern $pattern): void
    {
        foreach ($this->weekdays() as $weekday => $_label) {
            $menuId = $request->input("days.$weekday");
            $pattern->days()->updateOrCreate(['weekday' => $weekday], ['menu_id' => $menuId ?: null]);
        }
    }

    private function weekdays(): array
    {
        return [1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土', 0 => '日'];
    }

    private function authorizeOwner(Request $request, SchedulePattern $schedule): void
    {
        abort_unless($schedule->user_id === $request->user()->id, 403);
    }
}
