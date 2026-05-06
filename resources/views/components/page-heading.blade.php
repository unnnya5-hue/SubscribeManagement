<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-semibold">{{ $title }}</h1>
        @isset($description)<p class="mt-1 text-sm text-zinc-600">{{ $description }}</p>@endisset
    </div>
    @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
</div>
