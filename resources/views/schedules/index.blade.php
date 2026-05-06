<x-layouts.app title="スケジュール">
    <x-page-heading title="スケジュール" description="曜日ごとのメニュー割り当てを管理します。">
        <x-slot:actions><a class="rounded bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('schedules.create') }}">作成</a></x-slot:actions>
    </x-page-heading>
    @if($active)
        <section class="mb-6 rounded border border-zinc-200 bg-white p-4">
            <h2 class="font-semibold">有効パターン: {{ $active->name }}</h2>
            <div class="mt-4 grid grid-cols-2 gap-2 md:grid-cols-7">
                @foreach([1=>'月',2=>'火',3=>'水',4=>'木',5=>'金',6=>'土',0=>'日'] as $w => $label)
                    @php($day = $active->days->firstWhere('weekday', $w))
                    <div class="min-h-24 rounded border border-zinc-200 p-3"><div class="text-sm text-zinc-500">{{ $label }}</div><div class="mt-2 text-sm font-medium">{{ $day?->menu?->name ?? '休み' }}</div></div>
                @endforeach
            </div>
        </section>
    @endif
    <section class="rounded border border-zinc-200 bg-white">
        @forelse($patterns as $pattern)
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 p-4 last:border-0">
                <div><div class="font-medium">{{ $pattern->name }} @if($pattern->is_active)<span class="text-xs text-emerald-700">有効</span>@endif</div><div class="text-sm text-zinc-500">{{ $pattern->repeat_type }} / {{ $pattern->starts_on?->format('Y/m/d') ?? '-' }}</div></div>
                <div class="flex gap-2">
                    <form method="post" action="{{ route('schedules.activate', $pattern) }}">@csrf<button class="rounded border border-zinc-300 px-3 py-2 text-sm">有効化</button></form>
                    <a class="rounded border border-zinc-300 px-3 py-2 text-sm" href="{{ route('schedules.edit', $pattern) }}">編集</a>
                    <form method="post" action="{{ route('schedules.destroy', $pattern) }}">@csrf @method('delete')<button class="rounded border border-rose-300 px-3 py-2 text-sm text-rose-700">削除</button></form>
                </div>
            </div>
        @empty
            <p class="p-4 text-sm text-zinc-500">スケジュールがありません。</p>
        @endforelse
    </section>
</x-layouts.app>
