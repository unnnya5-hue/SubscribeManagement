@props(['label', 'description'])

<span class="inline-flex items-center gap-1">
    <span>{{ $label }}</span>
    <span tabindex="0" title="{{ $description }}" class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-zinc-300 text-xs font-semibold text-zinc-600">?</span>
</span>
