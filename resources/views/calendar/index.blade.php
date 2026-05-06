<x-layouts.app title="カレンダー">
    <x-page-heading :title="$month->format('Y年n月')" description="トレーニング実施日を月間で確認します。">
        <x-slot:actions>
            <a class="rounded border border-zinc-300 px-3 py-2 text-sm" href="{{ route('calendar.index', ['month' => $month->subMonth()->format('Y-m-01')]) }}">前月</a>
            <a class="rounded border border-zinc-300 px-3 py-2 text-sm" href="{{ route('calendar.index', ['month' => $month->addMonth()->format('Y-m-01')]) }}">翌月</a>
        </x-slot:actions>
    </x-page-heading>
    @php($start = $month->startOfMonth()->startOfWeek())
    <div class="grid grid-cols-7 rounded border border-zinc-200 bg-white">
        @foreach(['月','火','水','木','金','土','日'] as $label)<div class="border-b border-zinc-200 p-2 text-center text-sm text-zinc-500">{{ $label }}</div>@endforeach
        @for($i=0; $i<42; $i++)
            @php($date = $start->addDays($i))
            @php($items = $workouts->get($date->format('Y-m-d'), collect()))
            <div class="min-h-28 border-b border-r border-zinc-100 p-2 {{ $date->month !== $month->month ? 'bg-zinc-50 text-zinc-400' : '' }}">
                <div class="text-sm">{{ $date->day }}</div>
                @foreach($items as $workout)
                    <a class="mt-1 block rounded bg-zinc-900 px-2 py-1 text-xs text-white" href="{{ route('workouts.show', $workout) }}">{{ $workout->menu?->name ?? '記録' }}</a>
                @endforeach
            </div>
        @endfor
    </div>
</x-layouts.app>
