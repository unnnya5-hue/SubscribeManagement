<x-layouts.app title="ダッシュボード">
    <x-page-heading title="ダッシュボード" description="今日の予定、最近の記録、身体データをひと目で確認します。" />
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">最新体重</div><div class="mt-2 text-2xl font-semibold">{{ $latestBody?->weight_kg ?? '-' }} kg</div></div>
        <div class="rounded border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">30日変化</div><div class="mt-2 text-2xl font-semibold">{{ $bodyDelta ?? '-' }} kg</div></div>
        <div class="rounded border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">累計ワークアウト</div><div class="mt-2 text-2xl font-semibold">{{ $workoutCount }}</div></div>
        <a href="{{ route('workouts.index') }}" class="rounded border border-zinc-900 bg-zinc-900 p-4 text-white"><div class="text-sm text-zinc-300">今日も記録</div><div class="mt-2 text-lg font-semibold">ワークアウト開始</div></a>
    </div>
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded border border-zinc-200 bg-white p-4">
            <h2 class="font-semibold">今週の予定</h2>
            <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-7">
                @foreach ($week as $day)
                    <div class="min-h-24 rounded border border-zinc-200 p-3">
                        <div class="text-sm text-zinc-500">{{ $day['date']->format('m/d') }}</div>
                        <div class="mt-2 text-sm font-medium">{{ $day['menu']?->name ?? '休み' }}</div>
                    </div>
                @endforeach
            </div>
        </section>
        <section class="rounded border border-zinc-200 bg-white p-4">
            <h2 class="font-semibold">最近の記録</h2>
            <div class="mt-3 divide-y divide-zinc-100">
                @forelse ($recentWorkouts as $workout)
                    <a class="flex justify-between py-3 text-sm hover:bg-zinc-50" href="{{ route('workouts.show', $workout) }}"><span>{{ $workout->performed_on->format('Y/m/d') }}</span><span>{{ $workout->menu?->name ?? '手動' }}</span></a>
                @empty
                    <p class="py-4 text-sm text-zinc-500">まだ記録がありません。</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
