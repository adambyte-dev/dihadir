@props([
    'title',
    'description' => null,
    'icon' => null,
    'step' => null,
])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm']) }}>
    <div class="flex items-start gap-3 border-b border-slate-100 px-5 py-4">
        @if ($step)
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-900 text-[11px] font-bold text-white">{{ $step }}</span>
        @elseif ($icon)
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $icon }}"/>
                </svg>
            </span>
        @endif

        <div class="min-w-0">
            <h3 class="text-[14px] font-bold leading-snug text-slate-900">{{ $title }}</h3>
            @if ($description)
                <p class="mt-0.5 text-[11.5px] leading-relaxed text-slate-500">{{ $description }}</p>
            @endif
        </div>

        @isset($aside)
            <div class="ml-auto shrink-0">{{ $aside }}</div>
        @endisset
    </div>

    <div class="p-5">
        {{ $slot }}
    </div>
</section>
