<x-layouts.app title="ユーザー登録">
    <div class="mx-auto max-w-md">
        <x-page-heading title="ユーザー登録" description="個人用のトレーニング記録を作成します。" />
        <form method="post" action="{{ route('register') }}" class="space-y-4 rounded border border-zinc-200 bg-white p-5">
            @csrf
            <label class="block text-sm">名前<input class="mt-1 w-full rounded border-zinc-300" name="name" value="{{ old('name') }}" required></label>
            <label class="block text-sm">メール<input class="mt-1 w-full rounded border-zinc-300" type="email" name="email" value="{{ old('email') }}" required></label>
            <label class="block text-sm">パスワード<input class="mt-1 w-full rounded border-zinc-300" type="password" name="password" required></label>
            <label class="block text-sm">パスワード確認<input class="mt-1 w-full rounded border-zinc-300" type="password" name="password_confirmation" required></label>
            <button class="w-full rounded bg-zinc-900 px-4 py-2 text-white">登録</button>
        </form>
    </div>
</x-layouts.app>
