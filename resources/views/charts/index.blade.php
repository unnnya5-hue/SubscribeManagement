<x-layouts.app title="分析">
    <x-page-heading title="分析" description="種目別進捗、身体データ、部位別ボリュームを確認します。ボリュームは重量 x 回数です。" />
    <div class="mb-4">
        <select id="usageSelect" class="rounded border-zinc-300 text-sm"><option value="">全種目</option>@foreach($usages as $usage)<option value="{{ $usage->id }}">{{ $usage->name }}</option>@endforeach</select>
    </div>
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded border border-zinc-200 bg-white p-4"><h2 class="mb-3 font-semibold">種目別進捗 <x-term label="1RM" description="1回だけ挙げられる最大重量の推定値です。" /></h2><canvas id="progressChart" height="140"></canvas></section>
        <section class="rounded border border-zinc-200 bg-white p-4"><h2 class="mb-3 font-semibold">身体推移</h2><canvas id="bodyChart" height="140"></canvas></section>
        <section class="rounded border border-zinc-200 bg-white p-4 lg:col-span-2"><h2 class="mb-3 font-semibold">部位別ボリューム <x-term label="ボリューム" description="重量 x 回数で見たトレーニング量です。" /></h2><canvas id="partChart" height="90"></canvas></section>
    </div>
    <script>
        let progressChart;
        const loadProgress = () => fetch('{{ route('charts.progress') }}?usage_id=' + document.getElementById('usageSelect').value).then(r => r.json()).then(data => {
            progressChart?.destroy();
            progressChart = new Chart(document.getElementById('progressChart'), { type: 'line', data: { labels: data.labels, datasets: [{ label: '推定1RM', data: data.oneRm, borderColor: '#18181b' }, { label: '重量', data: data.weight, borderColor: '#0f766e' }, { label: 'ボリューム', data: data.volume, borderColor: '#a16207' }] } });
        });
        document.getElementById('usageSelect').addEventListener('change', loadProgress);
        loadProgress();
        fetch('{{ route('charts.body') }}').then(r => r.json()).then(data => new Chart(document.getElementById('bodyChart'), { type: 'line', data: { labels: data.labels, datasets: [{ label: '体重', data: data.weight, borderColor: '#18181b' }, { label: '体脂肪率', data: data.bodyFat, borderColor: '#0f766e' }] } }));
        fetch('{{ route('charts.body-parts') }}').then(r => r.json()).then(data => new Chart(document.getElementById('partChart'), { type: 'radar', data: { labels: data.labels, datasets: [{ label: '30日ボリューム', data: data.volume, borderColor: '#18181b', backgroundColor: 'rgba(24,24,27,.12)' }] } }));
    </script>
</x-layouts.app>
