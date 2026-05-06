<x-layouts.app title="ワークアウト">
    <x-page-heading title="ワークアウト" description="スケジュールまたは手動選択で記録を開始します。" />
    <form method="post" action="{{ route('workouts.store') }}" class="mb-6 grid gap-3 rounded border border-zinc-200 bg-white p-4 md:grid-cols-4">
        @csrf
        <label class="block text-sm md:col-span-2">メニュー<select class="mt-1 w-full rounded border-zinc-300" name="menu_id" required><option value="">選択</option>@foreach($menus as $menu)<option value="{{ $menu->id }}" @selected($todayMenu?->id === $menu->id)>{{ $menu->name }}</option>@endforeach</select></label>
        <label class="block text-sm">日付<input class="mt-1 w-full rounded border-zinc-300" type="date" name="performed_on" value="{{ now()->format('Y-m-d') }}" required></label>
        <button class="self-end rounded bg-zinc-900 px-4 py-2 text-white">開始</button>
    </form>
    <section class="rounded border border-zinc-200 bg-white">
        @forelse($workouts as $workout)
            <a class="flex justify-between border-b border-zinc-100 p-4 last:border-0 hover:bg-zinc-50" href="{{ route('workouts.show', $workout) }}"><span>{{ $workout->performed_on->format('Y/m/d') }}</span><span>{{ $workout->menu?->name ?? '手動' }}</span></a>
        @empty
            <p class="p-4 text-sm text-zinc-500">ワークアウト記録がありません。</p>
        @endforelse
    </section>
    <div class="mt-6">{{ $workouts->links() }}</div>
</x-layouts.app>
