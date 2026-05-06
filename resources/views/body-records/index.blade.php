<x-layouts.app title="身体データ">
    <x-page-heading title="身体データ" description="体重と体脂肪率の推移を記録します。">
        <x-slot:actions><a class="rounded bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('body-records.create') }}">新規記録</a></x-slot:actions>
    </x-page-heading>
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">最新体重</div><div class="mt-2 text-2xl font-semibold">{{ $latest?->weight_kg ?? '-' }} kg</div></div>
        <div class="rounded border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500"><x-term label="体脂肪率" description="体重のうち脂肪が占める割合です。" /></div><div class="mt-2 text-2xl font-semibold">{{ $latest?->body_fat_pct ?? '-' }} %</div></div>
        <div class="rounded border border-zinc-200 bg-white p-4"><div class="text-sm text-zinc-500">30日変化</div><div class="mt-2 text-2xl font-semibold">{{ $latest && $base ? round($latest->weight_kg - $base->weight_kg, 1) : '-' }} kg</div></div>
    </div>
    <section class="mb-6 rounded border border-zinc-200 bg-white p-4"><canvas id="bodyChart" height="100"></canvas></section>
    <section class="rounded border border-zinc-200 bg-white">
        @forelse($records as $record)
            <div class="grid gap-2 border-b border-zinc-100 p-4 text-sm last:border-0 md:grid-cols-5">
                <span class="font-medium">{{ $record->recorded_date->format('Y/m/d') }}</span><span>{{ $record->weight_kg }} kg</span><span>{{ $record->body_fat_pct ?? '-' }} %</span><span>{{ $record->notes }}</span><a class="text-zinc-700 underline" href="{{ route('body-records.edit', $record) }}">編集</a>
            </div>
        @empty
            <p class="p-4 text-sm text-zinc-500">身体データがありません。</p>
        @endforelse
    </section>
    <div class="mt-6">{{ $records->links() }}</div>
    <script>
        fetch('{{ route('charts.body') }}').then(r => r.json()).then(data => new Chart(document.getElementById('bodyChart'), {
            type: 'line',
            data: { labels: data.labels, datasets: [{ label: '体重', data: data.weight, borderColor: '#18181b' }, { label: '体脂肪率', data: data.bodyFat, borderColor: '#0f766e' }] },
            options: { responsive: true }
        }));
    </script>
</x-layouts.app>
