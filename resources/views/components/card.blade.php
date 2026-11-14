@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'padding' => true,
    'accent' => null, // blue, emerald, amber, slate, rose, sky
    'icon' => null,
])

@php
    $accentBar = [
        'blue' => 'bg-blue-500',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'slate' => 'bg-slate-600',
        'rose' => 'bg-rose-500',
        'sky' => 'bg-sky-500',
    ][$accent] ?? null;
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm']) }}>
    @if ($accentBar)
        <div class="absolute inset-x-0 top-0 h-[3px] {{ $accentBar }}" aria-hidden="true"></div>
    @endif

    @if ($title || $subtitle || isset($headerAction))
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
            <div class="flex min-w-0 items-start gap-3">
                @if ($icon)
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $icon }}"/>
                        </svg>
                    </span>
                @endif
                <div class="min-w-0">
                    @if ($title)
                        <h3 class="text-[15px] font-bold leading-snug text-slate-900">{{ $title }}</h3>
                    @endif
                    @if ($subtitle)
                        <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @isset($headerAction)
                <div class="shrink-0">{{ $headerAction }}</div>
            @endisset
        </div>
    @endif

    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-3.5 text-sm">
            {{ $footer }}
        </div>
    @endif
</div>
