<x-layouts.app title="身体データ編集">
    <x-page-heading :title="$record->exists ? '身体データ編集' : '身体データ新規記録'" description="同じ日付は1件だけ登録できます。" />
    <form method="post" action="{{ $record->exists ? route('body-records.update', $record) : route('body-records.store') }}" class="max-w-2xl space-y-4 rounded border border-zinc-200 bg-white p-4">
        @csrf
        @if($record->exists) @method('put') @endif
        <label class="block text-sm">日付<input class="mt-1 w-full rounded border-zinc-300" type="date" name="recorded_date" value="{{ old('recorded_date', $record->recorded_date?->format('Y-m-d')) }}" required></label>
        <label class="block text-sm">体重kg<input class="mt-1 w-full rounded border-zinc-300" type="number" step="0.1" name="weight_kg" value="{{ old('weight_kg', $record->weight_kg) }}" required></label>
        <label class="block text-sm"><x-term label="体脂肪率%" description="体重のうち脂肪が占める割合です。未測定なら空欄で大丈夫です。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" step="0.1" name="body_fat_pct" value="{{ old('body_fat_pct', $record->body_fat_pct) }}"></label>
        <label class="block text-sm">メモ<textarea class="mt-1 w-full rounded border-zinc-300" name="notes" rows="3">{{ old('notes', $record->notes) }}</textarea></label>
        <div class="flex gap-2"><button class="rounded bg-zinc-900 px-4 py-2 text-white">保存</button><a class="rounded border border-zinc-300 px-4 py-2" href="{{ route('body-records.index') }}">戻る</a></div>
    </form>
    @if($record->exists)
        <form method="post" action="{{ route('body-records.destroy', $record) }}" class="mt-4">@csrf @method('delete')<button class="rounded border border-rose-300 px-4 py-2 text-rose-700">削除</button></form>
    @endif
</x-layouts.app>
