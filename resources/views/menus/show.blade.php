<x-layouts.app title="メニュー詳細">
    <x-page-heading :title="$menu->name" :description="$menu->notes">
        <x-slot:actions>
            <a class="rounded border border-zinc-300 px-4 py-2 text-sm" href="{{ route('menus.suggest', $menu) }}">AI提案</a>
            <a class="rounded border border-zinc-300 px-4 py-2 text-sm" href="{{ route('menus.edit', $menu) }}">編集</a>
            <form method="post" action="{{ route('menus.destroy', $menu) }}">@csrf @method('delete')<button class="rounded border border-rose-300 px-4 py-2 text-sm text-rose-700">削除</button></form>
        </x-slot:actions>
    </x-page-heading>
    <section class="rounded border border-zinc-200 bg-white">
        @foreach ($menu->items as $item)
            <div class="grid gap-2 border-b border-zinc-100 p-4 text-sm last:border-0 md:grid-cols-5">
                <span class="font-medium md:col-span-2">{{ $item->usage?->machine?->name }} / {{ $item->usage?->name ?? '削除済み種目' }}</span>
                <span><x-term label="セット" description="同じ種目を続けて行うまとまりです。" /> {{ $item->sets }}</span>
                <span><x-term label="回数" description="1セットの中で何回動作したかです。レップとも呼びます。" /> {{ $item->reps }}</span>
                <span><x-term label="重量" description="その種目で扱う重さです。" /> {{ $item->weight_kg ?? '-' }} kg</span>
            </div>
        @endforeach
    </section>
</x-layouts.app>
