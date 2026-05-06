<x-layouts.app title="マシン詳細">
    <x-page-heading :title="$machine->name" :description="$machine->category ?? '未分類'">
        <x-slot:actions>
            <a class="rounded border border-zinc-300 px-4 py-2 text-sm" href="{{ route('machines.edit', $machine) }}">編集</a>
            <form method="post" action="{{ route('machines.destroy', $machine) }}">@csrf @method('delete')<button class="rounded border border-rose-300 px-4 py-2 text-sm text-rose-700">削除</button></form>
        </x-slot:actions>
    </x-page-heading>
    <section class="rounded border border-zinc-200 bg-white p-4">
        <dl class="grid gap-4 md:grid-cols-3">
            <div><dt class="text-sm text-zinc-500">場所</dt><dd class="font-medium">{{ $machine->location === 'gym' ? 'ジム' : '自宅' }}</dd></div>
            <div><dt class="text-sm text-zinc-500">カテゴリ</dt><dd class="font-medium">{{ $machine->category ?? '-' }}</dd></div>
            <div><dt class="text-sm text-zinc-500">備考</dt><dd class="font-medium">{{ $machine->notes ?? '-' }}</dd></div>
        </dl>
    </section>
    <section class="mt-6 rounded border border-zinc-200 bg-white p-4">
        <h2 class="font-semibold">登録済み種目</h2>
        <div class="mt-3 divide-y divide-zinc-100">
            @foreach ($machine->usages as $usage)
                <div class="grid gap-2 py-3 text-sm md:grid-cols-3"><span class="font-medium">{{ $usage->name }}</span><span>{{ $usage->body_part ?? '-' }}</span><span><x-term label="MET" description="運動の強度を表す目安です。大きいほど消費エネルギーが高い運動です。" /> {{ $usage->met_value }}</span></div>
            @endforeach
        </div>
    </section>
</x-layouts.app>
