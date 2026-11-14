@props([
    'variant' => 'default', // default, primary, secondary, success, warning, danger, info
    'size' => 'md', // sm, md
    'dot' => false,
])

@php
    $baseStyles = 'inline-flex items-center font-semibold rounded-md whitespace-nowrap align-middle';

    $variants = [
        'default' => 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200',
        'primary' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200',
        'secondary' => 'bg-slate-700 text-white',
        'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200',
        'warning' => 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200',
        'danger' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-200',
        'info' => 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-200',
    ];

    $dotColors = [
        'default' => 'bg-slate-400',
        'primary' => 'bg-blue-500',
        'secondary' => 'bg-white/80',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        'info' => 'bg-sky-500',
    ];

    $sizes = [
        'sm' => 'text-[10.5px] px-2 py-0.5 gap-1',
        'md' => 'text-xs px-2.5 py-1 gap-1.5',
    ];

    $selectedVariant = $variants[$variant] ?? $variants['default'];
    $selectedSize = $sizes[$size] ?? $sizes['md'];
    $dotColor = $dotColors[$variant] ?? $dotColors['default'];

    $classes = "{$baseStyles} {$selectedVariant} {$selectedSize}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $dotColor }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
