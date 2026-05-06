<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MenuItem;
use App\Models\PresetMachine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $machines = Machine::with('usages')
            ->where('user_id', $request->user()->id)
            ->when($request->filled('location'), fn ($query) => $query->where('location', $request->location))
            ->orderBy('sort_order')
            ->latest()
            ->paginate(15);

        return view('machines.index', compact('machines'));
    }

    public function create()
    {
        return view('machines.form', [
            'machine' => new Machine(['location' => 'gym']),
            'presets' => PresetMachine::with('usages')->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $machine = Machine::create($this->validatedMachine($request) + ['user_id' => $request->user()->id]);
        $this->syncUsages($request, $machine);

        return redirect()->route('machines.show', $machine)->with('status', 'マシンを登録しました。');
    }

    public function show(Request $request, Machine $machine)
    {
        $this->authorizeOwner($request, $machine);

        return view('machines.show', ['machine' => $machine->load('usages')]);
    }

    public function edit(Request $request, Machine $machine)
    {
        $this->authorizeOwner($request, $machine);

        return view('machines.form', [
            'machine' => $machine->load('usages'),
            'presets' => PresetMachine::with('usages')->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Machine $machine)
    {
        $this->authorizeOwner($request, $machine);
        $machine->update($this->validatedMachine($request));
        $this->syncUsages($request, $machine);

        return redirect()->route('machines.show', $machine)->with('status', 'マシンを更新しました。');
    }

    public function destroy(Request $request, Machine $machine)
    {
        $this->authorizeOwner($request, $machine);
        $usageIds = $machine->usages()->pluck('id');
        if (MenuItem::whereIn('machine_usage_id', $usageIds)->exists()) {
            return back()->withErrors('メニューで使用中のため削除できません。');
        }

        $machine->delete();

        return redirect()->route('machines.index')->with('status', 'マシンを削除しました。');
    }

    private function validatedMachine(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['required', 'in:gym,home'],
            'category' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }

    private function syncUsages(Request $request, Machine $machine): void
    {
        $usages = collect($request->input('usages', []))
            ->filter(fn ($usage) => filled($usage['name'] ?? null))
            ->values();

        $keptIds = [];
        foreach ($usages as $usage) {
            $existing = filled($usage['id'] ?? null)
                ? $machine->usages()->whereKey($usage['id'])->first()
                : null;

            $saved = $existing ?: $machine->usages()->make();
            $saved->fill([
                'name' => $usage['name'],
                'body_part' => $usage['body_part'] ?? null,
                'met_value' => $usage['met_value'] ?? 5.0,
                'notes' => $usage['notes'] ?? null,
            ]);
            $saved->save();
            $keptIds[] = $saved->id;
        }

        $machine->usages()
            ->whereNotIn('id', $keptIds)
            ->whereDoesntHave('menuItems')
            ->delete();
    }

    private function authorizeOwner(Request $request, Machine $machine): void
    {
        abort_unless($machine->user_id === $request->user()->id, 403);
    }
}
