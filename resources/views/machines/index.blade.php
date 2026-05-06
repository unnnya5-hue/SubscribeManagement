<x-layouts.app title="マシン">
    <x-page-heading title="マシン" description="器具と種目をまとめて管理します.">
        <x-slot:actions><a class="rounded bg-zinc-900 px-4 py-2 text-sm text-white" href="{{ route('machines.create') }}">新規登録</a></x-slot:actions>
    </x-page-heading>
    <form class="mb-4 flex gap-2">
        <select name="location" class="rounded border-zinc-300 text-sm">
            <option value="">すべて</option>
            <option value="gym" @selected(request('location')==='gym')>ジム</option>
            <option value="home" @selected(request('location')==='home')>自宅</option>
        </select>
        <button class="rounded border border-zinc-300 px-4 py-2 text-sm">絞り込み</button>
    </form>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($machines as $machine)
            <a href="{{ route('machines.show', $machine) }}" class="rounded border border-zinc-200 bg-white p-4 hover:border-zinc-400">
                <div class="flex justify-between gap-3">
                    <h2 class="font-semibold">{{ $machine->name }}</h2>
                    <span class="rounded bg-zinc-100 px-2 py-1 text-xs">{{ $machine->location === 'gym' ? 'ジム' : '自宅' }}</span>
                </div>
                <p class="mt-2 text-sm text-zinc-500">{{ $machine->category ?? '未分類' }}</p>
                <p class="mt-4 text-sm">{{ $machine->usages->count() }} 種目</p>
            </a>
        @empty
            <p class="text-sm text-zinc-500">マシンがありません。</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $machines->links() }}</div>
</x-layouts.app>
