<x-layouts.app title="マシン編集">
    <x-page-heading :title="$machine->exists ? 'マシン編集' : 'マシン新規登録'" description="プリセット選択または手入力で器具と種目を登録します。" />
    @php
        $presetPayload = $presets->map(fn ($preset) => [
            'name' => $preset->name,
            'location' => $preset->location,
            'category' => $preset->category,
            'notes' => $preset->notes,
            'usages' => $preset->usages->map(fn ($usage) => ['name' => $usage->name, 'body_part' => $usage->body_part, 'met_value' => $usage->met_value, 'notes' => ''])->values(),
        ])->values();
        $initialUsages = old('usages', $machine->exists ? $machine->usages->map(fn ($usage) => ['id' => $usage->id, 'name' => $usage->name, 'body_part' => $usage->body_part, 'met_value' => $usage->met_value, 'notes' => $usage->notes])->values()->all() : [['name' => '', 'body_part' => '', 'met_value' => 5.0, 'notes' => '']]);
    @endphp
    <form method="post" action="{{ $machine->exists ? route('machines.update', $machine) : route('machines.store') }}" class="space-y-6" x-data="{ presets: @js($presetPayload), usages: @js($initialUsages), applyPreset(i){ const p=this.presets[i]; if(!p) return; this.$refs.name.value=p.name; this.$refs.location.value=p.location; this.$refs.category.value=p.category; this.$refs.notes.value=p.notes; this.usages=p.usages; } }">
        @csrf
        @if($machine->exists) @method('put') @endif
        <section class="rounded border border-zinc-200 bg-white p-4">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-sm"><x-term label="プリセット" description="よく使う器具や種目をすぐ入力できる初期候補です。" /><select class="mt-1 w-full rounded border-zinc-300" @change="applyPreset($event.target.value)"><option value="">選択しない</option>@foreach($presets as $i => $preset)<option value="{{ $i }}">{{ $preset->category }} / {{ $preset->name }}</option>@endforeach</select></label>
                <label class="block text-sm">名前<input x-ref="name" class="mt-1 w-full rounded border-zinc-300" name="name" value="{{ old('name', $machine->name) }}" required></label>
                <label class="block text-sm">場所<select x-ref="location" class="mt-1 w-full rounded border-zinc-300" name="location"><option value="gym" @selected(old('location', $machine->location)==='gym')>ジム</option><option value="home" @selected(old('location', $machine->location)==='home')>自宅</option></select></label>
                <label class="block text-sm">カテゴリ<input x-ref="category" class="mt-1 w-full rounded border-zinc-300" name="category" value="{{ old('category', $machine->category) }}"></label>
                <label class="block text-sm md:col-span-2">備考<textarea x-ref="notes" class="mt-1 w-full rounded border-zinc-300" name="notes" rows="3">{{ old('notes', $machine->notes) }}</textarea></label>
            </div>
        </section>
        <section class="rounded border border-zinc-200 bg-white p-4">
            <div class="mb-3 flex justify-between"><h2 class="font-semibold">使い方（種目）</h2><button type="button" class="rounded border border-zinc-300 px-3 py-1 text-sm" @click="usages.push({name:'',body_part:'',met_value:5,notes:''})">追加</button></div>
            <template x-for="(usage, index) in usages" :key="index">
                <div class="mb-3 grid gap-3 rounded border border-zinc-200 p-3 md:grid-cols-5">
                    <input type="hidden" :name="`usages[${index}][id]`" x-model="usage.id">
                    <label class="block text-sm md:col-span-2">種目名<input class="mt-1 w-full rounded border-zinc-300" :name="`usages[${index}][name]`" x-model="usage.name"></label>
                    <label class="block text-sm">対象部位<input class="mt-1 w-full rounded border-zinc-300" :name="`usages[${index}][body_part]`" x-model="usage.body_part"></label>
                    <label class="block text-sm"><x-term label="MET" description="運動の強度を表す目安です。大きいほど消費エネルギーが高い運動です。" /><input class="mt-1 w-full rounded border-zinc-300" type="number" step="0.1" :name="`usages[${index}][met_value]`" x-model="usage.met_value"></label>
                    <button type="button" class="self-end rounded border border-zinc-300 px-3 py-2 text-sm" @click="usages.splice(index,1)">削除</button>
                </div>
            </template>
        </section>
        <div class="flex gap-2"><button class="rounded bg-zinc-900 px-4 py-2 text-white">保存</button><a class="rounded border border-zinc-300 px-4 py-2" href="{{ route('machines.index') }}">戻る</a></div>
    </form>
</x-layouts.app>
