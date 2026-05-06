<x-layouts.app title="ワークアウト記録">
    <x-page-heading :title="$workout->performed_on->format('Y/m/d') . ' ' . ($workout->menu?->name ?? '')" description="重量、回数、RPE、休憩時間を入力します。" />
    <form method="post" action="{{ route('workouts.update', $workout) }}" class="space-y-4">
        @csrf @method('put')
        <section class="rounded border border-zinc-200 bg-white">
            @foreach($workout->sets as $set)
                @php($prev = $previous?->sets->where('machine_usage_id', $set->machine_usage_id)->where('set_number', $set->set_number)->first())
                <div class="grid gap-3 border-b border-zinc-100 p-4 text-sm last:border-0 md:grid-cols-8">
                    <div class="font-medium md:col-span-2">{{ $set->usage?->name }}<div class="text-xs text-zinc-500">{{ $set->usage?->machine?->name }} / {{ $set->set_number }}セット目</div></div>
                    <label><x-term label="重量kg" description="このセットで扱った重さです。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" step="0.5" name="sets[{{ $set->id }}][weight_kg]" value="{{ $set->weight_kg }}"></label>
                    <label><x-term label="回数" description="このセットで動作した回数です。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" name="sets[{{ $set->id }}][reps]" value="{{ $set->reps }}"></label>
                    <label><x-term label="RPE" description="きつさの自己評価です。10に近いほど限界に近い状態です。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" step="0.5" name="sets[{{ $set->id }}][rpe]" value="{{ $set->rpe }}"></label>
                    <label><x-term label="休憩秒" description="セット後に休んだ時間を秒で記録します。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" name="sets[{{ $set->id }}][rest_seconds]" value="{{ $set->rest_seconds }}"></label>
                    <div><x-term label="1RM" description="1回だけ挙げられる最大重量の推定値です。エプレー式で自動計算します。" /><div class="mt-2 font-medium">{{ $set->estimated_1rm ?? '-' }}</div></div>
                    <div>前回<div class="mt-2 text-xs text-zinc-500">{{ $prev ? "{$prev->weight_kg}kg x {$prev->reps}" : '-' }}</div></div>
                </div>
            @endforeach
        </section>
        <label class="block text-sm">メモ<textarea class="mt-1 w-full rounded border-zinc-300" name="notes" rows="3">{{ $workout->notes }}</textarea></label>
        <div class="flex gap-2"><button class="rounded bg-zinc-900 px-4 py-2 text-white">保存</button><a class="rounded border border-zinc-300 px-4 py-2" href="{{ route('workouts.index') }}">一覧へ</a></div>
    </form>
</x-layouts.app>
