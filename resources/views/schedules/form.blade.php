<x-layouts.app title="スケジュール編集">
    <x-page-heading :title="$schedule->exists ? 'スケジュール編集' : 'スケジュール作成'" description="曜日ごとにメニューを割り当てます。" />
    <form method="post" action="{{ $schedule->exists ? route('schedules.update', $schedule) : route('schedules.store') }}" class="space-y-6">
        @csrf
        @if($schedule->exists) @method('put') @endif
        <section class="rounded border border-zinc-200 bg-white p-4">
            <div class="grid gap-4 md:grid-cols-3">
                <label class="block text-sm">名前<input class="mt-1 w-full rounded border-zinc-300" name="name" value="{{ old('name', $schedule->name) }}" required></label>
                <label class="block text-sm">繰り返し<select class="mt-1 w-full rounded border-zinc-300" name="repeat_type"><option value="weekly" @selected(old('repeat_type', $schedule->repeat_type)==='weekly')>毎週</option><option value="biweekly_a" @selected(old('repeat_type', $schedule->repeat_type)==='biweekly_a')>隔週A</option><option value="biweekly_b" @selected(old('repeat_type', $schedule->repeat_type)==='biweekly_b')>隔週B</option></select></label>
                <label class="block text-sm">開始日<input class="mt-1 w-full rounded border-zinc-300" type="date" name="starts_on" value="{{ old('starts_on', $schedule->starts_on?->format('Y-m-d')) }}"></label>
            </div>
        </section>
        <section class="rounded border border-zinc-200 bg-white p-4">
            <div class="grid gap-3 md:grid-cols-7">
                @foreach($weekdays as $weekday => $label)
                    @php($selected = old("days.$weekday", $schedule->days?->firstWhere('weekday', $weekday)?->menu_id))
                    <label class="block text-sm">{{ $label }}<select class="mt-1 w-full rounded border-zinc-300" name="days[{{ $weekday }}]"><option value="">休み</option>@foreach($menus as $menu)<option value="{{ $menu->id }}" @selected((string)$selected === (string)$menu->id)>{{ $menu->name }}</option>@endforeach</select></label>
                @endforeach
            </div>
        </section>
        <div class="flex gap-2"><button class="rounded bg-zinc-900 px-4 py-2 text-white">保存</button><a class="rounded border border-zinc-300 px-4 py-2" href="{{ route('schedules.index') }}">戻る</a></div>
    </form>
</x-layouts.app>
