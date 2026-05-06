<!doctype html>
<html lang="ja" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '筋トレ管理' }}</title>
    <script>tailwind = { config: { darkMode: 'class' } }; if (localStorage.theme === 'dark') document.documentElement.classList.add('dark');</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak]{display:none!important}
        .dark body{background:#09090b;color:#f4f4f5}
        .dark .bg-white{background:#18181b}
        .dark .bg-zinc-50{background:#09090b}
        .dark .border-zinc-200,.dark .border-zinc-100{border-color:#3f3f46}
        .dark .text-zinc-900{color:#f4f4f5}
        .dark .text-zinc-700,.dark .text-zinc-600,.dark .text-zinc-500{color:#a1a1aa}
        .dark input,.dark select,.dark textarea{background:#09090b;color:#f4f4f5;border-color:#52525b}
    </style>
</head>
<body class="min-h-full bg-zinc-50 text-zinc-900">
    <div class="min-h-screen">
        <header class="border-b border-zinc-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4 py-3">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold tracking-normal">筋トレ管理</a>
                @auth
                    <nav class="flex flex-wrap gap-1 text-sm">
                        @foreach ([
                            ['dashboard', 'ホーム'],
                            ['machines.index', 'マシン'],
                            ['menus.index', 'メニュー'],
                            ['schedules.index', 'スケジュール'],
                            ['workouts.index', '記録'],
                            ['body-records.index', '身体'],
                            ['charts.index', '分析'],
                            ['calendar.index', 'カレンダー'],
                        ] as [$route, $label])
                            <a class="rounded px-3 py-2 {{ request()->routeIs($route) ? 'bg-zinc-900 text-white' : 'hover:bg-zinc-100' }}" href="{{ route($route) }}">{{ $label }}</a>
                        @endforeach
                    </nav>
                    <div class="ml-auto flex items-center gap-2 text-sm" x-data="{ glossary: false }">
                        <a class="rounded px-3 py-2 hover:bg-zinc-100" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                        <button type="button" class="rounded border border-zinc-300 px-3 py-2" @click="glossary = true">用語集</button>
                        <label class="flex items-center gap-2 rounded px-2 py-2" x-data="{ dark: document.documentElement.classList.contains('dark') }">
                            <input type="checkbox" class="rounded" x-model="dark" @change="localStorage.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark)">
                            <span>Dark</span>
                        </label>
                        <form method="post" action="{{ route('logout') }}">@csrf<button class="rounded bg-zinc-900 px-3 py-2 text-white">ログアウト</button></form>
                        <div x-cloak x-show="glossary" class="fixed inset-0 z-50 bg-black/40 p-4" @click.self="glossary = false">
                            <section class="mx-auto mt-12 max-w-2xl rounded border border-zinc-200 bg-white p-5 text-zinc-900 shadow-xl">
                                <div class="mb-4 flex items-center justify-between">
                                    <h2 class="text-lg font-semibold">用語集</h2>
                                    <button type="button" class="rounded border border-zinc-300 px-3 py-1 text-sm" @click="glossary = false">閉じる</button>
                                </div>
                                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                                    <div><dt class="font-semibold">セット</dt><dd class="text-zinc-600">同じ種目を続けて行うまとまりです。</dd></div>
                                    <div><dt class="font-semibold">レップ</dt><dd class="text-zinc-600">1セットの中で何回動作したかです。</dd></div>
                                    <div><dt class="font-semibold">RPE</dt><dd class="text-zinc-600">きつさの自己評価です。10に近いほど限界に近い状態です。</dd></div>
                                    <div><dt class="font-semibold">推定1RM</dt><dd class="text-zinc-600">1回だけ挙げられる最大重量の推定値です。</dd></div>
                                    <div><dt class="font-semibold">MET</dt><dd class="text-zinc-600">運動の強度を表す目安です。大きいほど消費エネルギーが高い運動です。</dd></div>
                                    <div><dt class="font-semibold">ボリューム</dt><dd class="text-zinc-600">重量 x 回数で見たトレーニング量です。</dd></div>
                                    <div><dt class="font-semibold">体脂肪率</dt><dd class="text-zinc-600">体重のうち脂肪が占める割合です。</dd></div>
                                    <div><dt class="font-semibold">プリセット</dt><dd class="text-zinc-600">よく使う器具や種目をすぐ入力できる初期候補です。</dd></div>
                                </dl>
                            </section>
                        </div>
                    </div>
                @else
                    <div class="ml-auto flex gap-2 text-sm">
                        <a class="rounded px-3 py-2 hover:bg-zinc-100" href="{{ route('login') }}">ログイン</a>
                        <a class="rounded bg-zinc-900 px-3 py-2 text-white" href="{{ route('register') }}">登録</a>
                    </div>
                @endauth
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-6">
            @if (session('status'))
                <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>
</html>
