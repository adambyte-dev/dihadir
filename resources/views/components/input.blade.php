@props([
    'disabled' => false,
    'error' => null,
    'label' => null,
    'id' => null,
    'helper' => null,
    'required' => false,
    'leadingIcon' => null,
])

@php
    $inputId = $id ?? ($attributes->get('name') ?? 'input-'.\Illuminate\Support\Str::random(6));
    $hasError = ! empty($error);

    $inputClasses = 'block w-full rounded-lg text-sm transition duration-150 py-2.5 px-3.5 bg-white text-slate-900 border shadow-sm '
        .($hasError
            ? 'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-100'
            : 'border-slate-300 hover:border-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none')
        .($disabled ? ' bg-slate-100 text-slate-500 cursor-not-allowed hover:border-slate-300' : '')
        .' placeholder:text-slate-500';
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $inputId }}" class="mb-1.5 block text-[13px] font-semibold text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="ml-0.5 font-semibold text-red-500" title="Wajib diisi" aria-hidden="true">*</span>
                <span class="sr-only">(wajib diisi)</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $inputId }}"
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        @if ($disabled) disabled @endif
        {{ $attributes->merge(['class' => $inputClasses]) }}
    >

    @if ($hasError)
        <p id="{{ $inputId }}-error" class="mt-1.5 flex items-start gap-1 text-xs font-medium text-red-600">
            <svg class="mt-px h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $error }}</span>
        </p>
    @elseif ($helper)
        <p class="mt-1.5 text-xs leading-relaxed text-slate-500">{{ $helper }}</p>
    @endif
</div>
