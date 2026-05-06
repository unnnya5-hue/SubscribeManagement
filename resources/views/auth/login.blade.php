<x-layouts.app title="ログイン">
    <div class="mx-auto max-w-md">
        <x-page-heading title="ログイン" description="記録の続きから始めます。" />
        <form method="post" action="{{ route('login') }}" class="space-y-4 rounded border border-zinc-200 bg-white p-5">
            @csrf
            <label class="block text-sm">メール<input class="mt-1 w-full rounded border-zinc-300" type="email" name="email" value="{{ old('email') }}" required autofocus></label>
            <label class="block text-sm">パスワード<input class="mt-1 w-full rounded border-zinc-300" type="password" name="password" required></label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember">ログイン状態を保持</label>
            <button class="w-full rounded bg-zinc-900 px-4 py-2 text-white">ログイン</button>
        </form>
    </div>
</x-layouts.app>
