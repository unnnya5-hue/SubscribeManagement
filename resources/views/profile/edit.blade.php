<x-layouts.app title="プロフィール">
    <x-page-heading title="プロフィール" description="名前、メール、パスワードを変更します。" />
    <form method="post" action="{{ route('profile.update') }}" class="max-w-2xl space-y-4 rounded border border-zinc-200 bg-white p-4">
        @csrf @method('patch')
        <label class="block text-sm">名前<input class="mt-1 w-full rounded border-zinc-300" name="name" value="{{ old('name', $user->name) }}" required></label>
        <label class="block text-sm">メール<input class="mt-1 w-full rounded border-zinc-300" type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
        <label class="block text-sm">新しいパスワード<input class="mt-1 w-full rounded border-zinc-300" type="password" name="password"></label>
        <label class="block text-sm">新しいパスワード確認<input class="mt-1 w-full rounded border-zinc-300" type="password" name="password_confirmation"></label>
        <button class="rounded bg-zinc-900 px-4 py-2 text-white">保存</button>
    </form>
</x-layouts.app>
