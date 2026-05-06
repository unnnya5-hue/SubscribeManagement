<?php

namespace App\Http\Controllers;

use App\Models\MachineUsage;
use App\Models\Menu;
use App\Services\TrainingMetrics;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $menus = Menu::withCount('items')->where('user_id', $request->user()->id)->latest()->paginate(15);

        return view('menus.index', compact('menus'));
    }

    public function create(Request $request)
    {
        return view('menus.form', $this->formData($request, new Menu()));
    }

    public function store(Request $request)
    {
        $menu = Menu::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]) + ['user_id' => $request->user()->id]);
        $this->syncItems($request, $menu);

        return redirect()->route('menus.show', $menu)->with('status', 'メニューを作成しました。');
    }

    public function show(Request $request, Menu $menu)
    {
        $this->authorizeOwner($request, $menu);

        return view('menus.show', ['menu' => $menu->load('items.usage.machine')]);
    }

    public function edit(Request $request, Menu $menu)
    {
        $this->authorizeOwner($request, $menu);

        return view('menus.form', $this->formData($request, $menu->load('items')));
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorizeOwner($request, $menu);
        $menu->update($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]));
        $this->syncItems($request, $menu);

        return redirect()->route('menus.show', $menu)->with('status', 'メニューを更新しました。');
    }

    public function destroy(Request $request, Menu $menu)
    {
        $this->authorizeOwner($request, $menu);
        $menu->delete();

        return redirect()->route('menus.index')->with('status', 'メニューを削除しました。');
    }

    public function suggest(Request $request, Menu $menu, TrainingMetrics $metrics)
    {
        $this->authorizeOwner($request, $menu);
        $level = $request->string('level', 'normal')->toString();
        $suggestions = $menu->load('items.usage.machine')->items->mapWithKeys(function ($item) use ($metrics, $request, $level) {
            return [$item->id => $metrics->suggestion(
                $request->user()->id,
                (int) $item->machine_usage_id,
                (float) ($item->weight_kg ?? 0),
                (int) $item->reps,
                $level
            )];
        });

        return view('menus.suggest', compact('menu', 'level', 'suggestions'));
    }

    public function applySuggestion(Request $request, Menu $menu, TrainingMetrics $metrics)
    {
        $this->authorizeOwner($request, $menu);
        $level = $request->validate(['level' => ['required', 'in:easy,normal,hard,extreme']])['level'];
        foreach ($menu->items as $item) {
            $item->update($metrics->suggestion($request->user()->id, (int) $item->machine_usage_id, (float) ($item->weight_kg ?? 0), (int) $item->reps, $level));
        }

        return redirect()->route('menus.show', $menu)->with('status', 'AI重量提案を適用しました。');
    }

    private function formData(Request $request, Menu $menu): array
    {
        return [
            'menu' => $menu,
            'usages' => MachineUsage::with('machine')->whereHas('machine', fn ($query) => $query->where('user_id', $request->user()->id))->orderBy('name')->get(),
        ];
    }

    private function syncItems(Request $request, Menu $menu): void
    {
        $items = collect($request->input('items', []))->filter(fn ($item) => filled($item['machine_usage_id'] ?? null))->values();
        $menu->items()->delete();
        foreach ($items as $index => $item) {
            $menu->items()->create([
                'machine_usage_id' => $item['machine_usage_id'],
                'sort_order' => $index,
                'sets' => max(1, (int) ($item['sets'] ?? 3)),
                'reps' => max(1, (int) ($item['reps'] ?? 10)),
                'weight_kg' => $item['weight_kg'] ?? null,
            ]);
        }
    }

    private function authorizeOwner(Request $request, Menu $menu): void
    {
        abort_unless($menu->user_id === $request->user()->id, 403);
    }
}
