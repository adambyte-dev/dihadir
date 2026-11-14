@props([
    'type' => 'info', // success, error, warning, info
    'title' => null,
    'dismissible' => false,
])

@php
    $typeClasses = [
        'success' => 'bg-emerald-50/90 border-emerald-200 text-emerald-900',
        'error' => 'bg-red-50/90 border-red-200 text-red-900',
        'warning' => 'bg-amber-50/90 border-amber-200 text-amber-900',
        'info' => 'bg-sky-50/90 border-sky-200 text-sky-900',
    ];

    $iconWrapper = [
        'success' => 'bg-emerald-100 text-emerald-600',
        'error' => 'bg-red-100 text-red-600',
        'warning' => 'bg-amber-100 text-amber-600',
        'info' => 'bg-sky-100 text-sky-600',
    ];

    $selectedType = $typeClasses[$type] ?? $typeClasses['info'];
    $selectedIcon = $iconWrapper[$type] ?? $iconWrapper['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition.opacity.duration.200ms
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border p-3.5 text-sm shadow-sm ' . $selectedType]) }}
>
    <span class="mt-px flex h-6 w-6 shrink-0 items-center justify-center rounded-md {{ $selectedIcon }}">
        @if ($type === 'success')
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/>
            </svg>
        @elseif ($type === 'error')
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        @elseif ($type === 'warning')
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        @else
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @endif
    </span>

    <div class="min-w-0 flex-1">
        @if ($title)
            <p class="mb-0.5 text-[13px] font-bold">{{ $title }}</p>
        @endif
        <div class="text-[13px] leading-relaxed">
            {{ $slot }}
        </div>
    </div>

    @if ($dismissible)
        <button
            type="button"
            @click="show = false"
            class="-m-1.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-md opacity-70 transition-opacity hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-current"
        >
            <span class="sr-only">Tutup notifikasi</span>
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</div>
