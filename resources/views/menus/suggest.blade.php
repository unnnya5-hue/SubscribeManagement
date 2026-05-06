<x-layouts.app title="AI重量提案">
    <x-page-heading title="AI重量提案" :description="$menu->name">
        <x-slot:actions>
            @foreach(['easy' => 'easy', 'normal' => 'normal', 'hard' => 'hard', 'extreme' => 'extreme'] as $key => $label)
                <a class="rounded px-3 py-2 text-sm {{ $level === $key ? 'bg-zinc-900 text-white' : 'border border-zinc-300' }}" href="{{ route('menus.suggest', [$menu, 'level' => $key]) }}">{{ $label }}</a>
            @endforeach
        </x-slot:actions>
    </x-page-heading>
    <section class="rounded border border-zinc-200 bg-white">
        @foreach ($menu->items as $item)
            <div class="grid gap-2 border-b border-zinc-100 p-4 text-sm last:border-0 md:grid-cols-4">
                <span class="font-medium">{{ $item->usage?->machine?->name }} / {{ $item->usage?->name }}</span>
                <span>現状 {{ $item->weight_kg ?? '-' }} kg x {{ $item->reps }}</span>
                <span>提案 {{ $suggestions[$item->id]['weight_kg'] }} kg x {{ $suggestions[$item->id]['reps'] }}</span>
            </div>
        @endforeach
    </section>
    <form method="post" action="{{ route('menus.suggest.apply', $menu) }}" class="mt-4">
        @csrf
        <input type="hidden" name="level" value="{{ $level }}">
        <button class="rounded bg-zinc-900 px-4 py-2 text-white">一括適用</button>
    </form>
</x-layouts.app>
