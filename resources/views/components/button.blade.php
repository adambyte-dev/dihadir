@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, outline, ghost, danger, success
    'size' => 'md', // sm, md, lg
    'fullWidth' => false,
    'href' => null,
])

@php
    $baseStyles = 'inline-flex items-center justify-center font-semibold rounded-lg whitespace-nowrap select-none transition-all duration-150 '
        .'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 '
        .'disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none active:translate-y-px';

    $variants = [
        'primary' => 'bg-blue-600 text-white shadow-sm shadow-blue-600/25 hover:bg-blue-700 hover:shadow-md hover:shadow-blue-600/25 focus-visible:ring-blue-500',
        'secondary' => 'bg-slate-800 text-white shadow-sm hover:bg-slate-900 focus-visible:ring-slate-600',
        'outline' => 'bg-white text-slate-700 border border-slate-300 shadow-sm hover:bg-slate-50 hover:border-slate-400 hover:text-slate-900 focus-visible:ring-blue-500',
        'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-slate-400',
        'danger' => 'bg-red-600 text-white shadow-sm shadow-red-600/25 hover:bg-red-700 focus-visible:ring-red-500',
        'success' => 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/25 hover:bg-emerald-700 focus-visible:ring-emerald-500',
    ];

    $sizes = [
        'sm' => 'text-xs px-3 py-2 gap-1.5 min-h-[44px]',
        'md' => 'text-[13px] px-4 py-2.5 gap-2 min-h-[44px]',
        'lg' => 'text-sm px-5 py-3 gap-2.5 min-h-[48px]',
    ];

    $selectedVariant = $variants[$variant] ?? $variants['primary'];
    $selectedSize = $sizes[$size] ?? $sizes['md'];
    $widthClass = $fullWidth ? 'w-full' : '';

    $classes = trim("{$baseStyles} {$selectedVariant} {$selectedSize} {$widthClass}");
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
