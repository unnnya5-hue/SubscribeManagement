<x-layouts.app title="メニュー">
    <x-page-heading title="メニュー" description="種目、セット、重量の組み合わせを管理します。セットは同じ種目を行うまとまりです。">
        <x-slot:actions><a class="rounded bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('menus.create') }}">作成</a></x-slot:actions>
    </x-page-heading>
    <div class="rounded border border-zinc-200 bg-white">
        @forelse ($menus as $menu)
            <a class="flex items-center justify-between border-b border-zinc-100 p-4 last:border-0 hover:bg-zinc-50" href="{{ route('menus.show', $menu) }}">
                <span class="font-medium">{{ $menu->name }}</span>
                <span class="text-sm text-zinc-500">{{ $menu->items_count }} 種目</span>
            </a>
        @empty
            <p class="p-4 text-sm text-zinc-500">メニューがありません。</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $menus->links() }}</div>
</x-layouts.app>
