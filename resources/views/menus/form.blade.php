<x-layouts.app title="メニュー編集">
    <x-page-heading :title="$menu->exists ? 'メニュー編集' : 'メニュー作成'" description="種目の順番、セット数、レップ数、重量を設定します。" />
    @php
        $initialItems = old('items', $menu->exists ? $menu->items->map(fn ($item) => ['machine_usage_id' => $item->machine_usage_id, 'sets' => $item->sets, 'reps' => $item->reps, 'weight_kg' => $item->weight_kg])->values()->all() : [['machine_usage_id' => '', 'sets' => 3, 'reps' => 10, 'weight_kg' => '']]);
    @endphp
    <form method="post" action="{{ $menu->exists ? route('menus.update', $menu) : route('menus.store') }}" class="space-y-6" x-data="{ items: @js($initialItems) }">
        @csrf
        @if($menu->exists) @method('put') @endif
        <section class="rounded border border-zinc-200 bg-white p-4">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-sm">メニュー名<input class="mt-1 w-full rounded border-zinc-300" name="name" value="{{ old('name', $menu->name) }}" required></label>
                <label class="block text-sm md:col-span-2">メモ<textarea class="mt-1 w-full rounded border-zinc-300" name="notes" rows="2">{{ old('notes', $menu->notes) }}</textarea></label>
            </div>
        </section>
        <section class="rounded border border-zinc-200 bg-white p-4">
            <div class="mb-3 flex justify-between"><h2 class="font-semibold">種目</h2><button type="button" class="rounded border border-zinc-300 px-3 py-1 text-sm" @click="items.push({machine_usage_id:'',sets:3,reps:10,weight_kg:''})">追加</button></div>
            <template x-for="(item, index) in items" :key="index">
                <div class="mb-3 grid gap-3 rounded border border-zinc-200 p-3 md:grid-cols-6">
                    <label class="block text-sm md:col-span-2">種目<select class="mt-1 w-full rounded border-zinc-300" :name="`items[${index}][machine_usage_id]`" x-model="item.machine_usage_id"><option value="">選択</option>@foreach($usages as $usage)<option value="{{ $usage->id }}">{{ $usage->machine->name }} / {{ $usage->name }}</option>@endforeach</select></label>
                    <label class="block text-sm"><x-term label="セット" description="同じ種目を続けて行うまとまりです。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" min="1" :name="`items[${index}][sets]`" x-model="item.sets"></label>
                    <label class="block text-sm"><x-term label="回数" description="1セットの中で何回動作したかです。レップとも呼びます。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" min="1" :name="`items[${index}][reps]`" x-model="item.reps"></label>
                    <label class="block text-sm"><x-term label="重量kg" description="その種目で扱う重さです。自重種目などは空欄でも使えます。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" step="0.5" :name="`items[${index}][weight_kg]`" x-model="item.weight_kg"></label>
                    <button type="button" class="self-end rounded border border-zinc-300 px-3 py-2 text-sm" @click="items.splice(index,1)">削除</button>
                </div>
            </template>
        </section>
        <div class="flex gap-2"><button class="rounded bg-zinc-900 px-4 py-2 text-white">保存</button><a class="rounded border border-zinc-300 px-4 py-2" href="{{ route('menus.index') }}">戻る</a></div>
    </form>
</x-layouts.app>
