@props([
    'title',
    'description' => null,
    'backRoute' => null,
    'backLabel' => 'Kembali',
    'eyebrow' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($backRoute)
            <a
                href="{{ $backRoute }}"
                class="mb-2 inline-flex items-center gap-1.5 rounded-md text-[11px] font-bold uppercase tracking-[0.06em] text-slate-500 transition-colors hover:text-blue-600"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>{{ $backLabel }}</span>
            </a>
        @endif

        @if ($eyebrow)
            <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.13em] text-blue-600">{{ $eyebrow }}</p>
        @endif

        <h2 class="text-lg font-extrabold leading-tight tracking-tight text-slate-900 sm:text-xl">{{ $title }}</h2>

        @if ($description)
            <p class="mt-1 max-w-2xl text-[13px] leading-relaxed text-slate-500">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
