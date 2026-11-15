@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'options' => [],
    'placeholder' => '-- Pilih salah satu --',
    'required' => false,
    'error' => null,
    'disabled' => false,
    'size' => 'md', // sm, md, lg
    'autoSubmit' => false,
])

@php
    $inputId = $id ?? $name;
    $selectedValue = old($name, $value);
    
    // Support options as associative [value => label] or indexed ['A', 'B']
    $isList = is_array($options) && array_is_list($options);
    $optionsMap = [];
    foreach ($options as $key => $opt) {
        if ($isList) {
            $optionsMap[(string) $opt] = (string) $opt;
        } else {
            $optionsMap[(string) $key] = (string) $opt;
        }
    }
    $initialLabel = $optionsMap[(string) $selectedValue] ?? $placeholder;
    $heightClass = match($size) {
        'sm' => 'min-h-[36px] py-1.5 px-3 text-xs',
        'lg' => 'min-h-[46px] py-2.5 px-4 text-sm',
        default => 'min-h-[42px] py-2 px-3.5 text-xs sm:text-sm',
    };
@endphp

<div
    {{ $attributes->class('relative w-full') }}
    x-data="{
        open: false,
        selected: {{ \Illuminate\Support\Js::from((string) $selectedValue) }},
        selectedLabel: {{ \Illuminate\Support\Js::from($initialLabel) }},
        options: {{ \Illuminate\Support\Js::from($optionsMap) }},
        placeholder: {{ \Illuminate\Support\Js::from($placeholder) }},

        select(key, label) {
            this.selected = key;
            this.selectedLabel = label;
            this.open = false;
            this.$dispatch('input', key);
            this.$dispatch('change', key);
            @if ($autoSubmit)
                this.$nextTick(() => {
                    const form = this.$el.closest('form');
                    if (form) form.submit();
                });
            @endif
        }
    }"
    @click.away="open = false"
    @keydown.escape="open = false"
>
    @if ($label)
        <label for="{{ $inputId }}" class="mb-1.5 block text-[13px] font-semibold text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="font-bold text-rose-600" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" :value="selected">

    <button
        type="button"
        @click="if (!{{ $disabled ? 'true' : 'false' }}) open = !open"
        :aria-expanded="open"
        class="flex w-full items-center justify-between gap-2 rounded-lg border bg-white text-left text-slate-800 shadow-2xs transition-all hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 {{ $heightClass }} {{ $error ? 'border-red-400' : 'border-slate-300' }} {{ $disabled ? 'bg-slate-100 cursor-not-allowed' : 'cursor-pointer' }}"
    >
        <span 
            x-text="selected !== '' ? selectedLabel : placeholder" 
            class="truncate" 
            :class="(selected === '' || selected === null) && 'text-slate-400'"
        >{{ $initialLabel }}</span>
        <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute left-0 right-0 z-50 mt-1 max-h-60 overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-lg ring-1 ring-slate-900/5"
    >
        @forelse ($optionsMap as $key => $label)
            <button
                type="button"
                @click="select('{{ addslashes((string) $key) }}', '{{ addslashes((string) $label) }}')"
                role="option"
                :aria-selected="selected == '{{ addslashes((string) $key) }}'"
                class="flex w-full cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-xs text-slate-700 transition hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-50 focus:outline-none"
                :class="selected == '{{ addslashes((string) $key) }}' && 'bg-blue-50/70 font-semibold text-blue-700'"
            >
                <span class="truncate">{{ $label }}</span>
                <span x-show="selected == '{{ addslashes((string) $key) }}'" class="shrink-0 text-blue-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
            </button>
        @empty
            <div class="px-3 py-4 text-center text-xs text-slate-400">
                Tidak ada opsi pilihan
            </div>
        @endforelse
    </div>

    @if ($error)
        <p class="mt-1 flex items-start gap-1 text-[11px] font-medium text-red-600">
            <svg class="mt-0.5 h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
