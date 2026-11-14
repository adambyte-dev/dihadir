@props([
    'name' => 'kabupaten_kota',
    'label' => 'Kabupaten / Kota',
    'value' => 'Kabupaten Majalengka',
    'required' => true,
    'error' => null,
])

@php
    $regions = [
        'Kabupaten Majalengka',
        'Kabupaten Cirebon',
        'Kota Cirebon',
        'Kabupaten Kuningan',
        'Kabupaten Indramayu',
        'Kabupaten Sumedang',
        'Kabupaten Bandung',
        'Kota Bandung',
        'Kabupaten Bandung Barat',
        'Kota Cimahi',
        'Kabupaten Subang',
        'Kabupaten Purwakarta',
        'Kabupaten Karawang',
        'Kabupaten Bekasi',
        'Kota Bekasi',
        'Kabupaten Bogor',
        'Kota Bogor',
        'Kota Depok',
        'Kabupaten Sukabumi',
        'Kota Sukabumi',
        'Kabupaten Cianjur',
        'Kabupaten Garut',
        'Kabupaten Tasikmalaya',
        'Kota Tasikmalaya',
        'Kabupaten Ciamis',
        'Kota Banjar',
        'Kabupaten Pangandaran',
        'DKI Jakarta',
        'Kota Semarang',
        'Kota Surabaya',
        'Kota Yogyakarta',
        'Lainnya (Luar Wilayah)',
    ];
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selected: {{ \Illuminate\Support\Js::from((string) old($name, $value)) }},
        options: {{ \Illuminate\Support\Js::from($regions) }},

        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(opt => opt.toLowerCase().includes(this.search.toLowerCase()));
        },

        select(opt) {
            this.selected = opt;
            this.open = false;
            this.search = '';
        }
    }"
    @click.away="open = false"
    class="relative w-full"
>
    <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">
        {{ $label }}
        @if ($required)
            <span class="font-bold text-rose-600" aria-hidden="true">*</span>
            <span class="sr-only">(wajib diisi)</span>
        @endif
    </label>

    <input type="hidden" name="{{ $name }}" :value="selected">

    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        aria-haspopup="listbox"
        class="flex min-h-[44px] w-full items-center justify-between gap-2 rounded-lg border bg-white px-3.5 text-left text-[13px] text-slate-900 shadow-sm transition-colors hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 @error($name) border-rose-400 @else border-slate-300 @enderror"
    >
        <span class="flex min-w-0 items-center gap-2">
            <svg class="h-3.5 w-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-text="selected || 'Pilih Kabupaten/Kota…'" class="truncate" :class="!selected && 'text-slate-500'"></span>
        </span>
        <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition.opacity
        x-cloak
        class="absolute left-0 right-0 z-50 mt-1 flex max-h-60 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
    >
        <div class="border-b border-slate-100 bg-slate-50/80 p-2">
            <div class="relative">
                <svg class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Cari wilayah…"
                    aria-label="Cari kabupaten atau kota"
                    class="min-h-[44px] w-full rounded-md border border-slate-300 bg-white py-1.5 pl-8 pr-2.5 text-[12px] text-slate-900 placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/25"
                    @keydown.escape="open = false"
                    @keydown.enter.prevent="if (filteredOptions.length) select(filteredOptions[0])"
                    x-ref="searchInput"
                    x-init="$watch('open', value => { if (value) setTimeout(() => $refs.searchInput.focus(), 50) })"
                >
            </div>
        </div>

        <ul class="max-h-48 overflow-y-auto py-1" role="listbox">
            <template x-for="opt in filteredOptions" :key="opt">
                <li
                    @click="select(opt)"
                    @keydown.enter.prevent="select(opt)"
                    @keydown.space.prevent="select(opt)"
                    tabindex="0"
                    role="option"
                    :aria-selected="selected === opt"
                    class="flex cursor-pointer items-center justify-between gap-2 px-3.5 py-2 text-[12.5px] text-slate-700 transition-colors hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-50 focus:outline-none"
                    :class="selected === opt && 'bg-blue-50/70 font-semibold text-blue-700'"
                >
                    <span x-text="opt" class="truncate"></span>
                    <span x-show="selected === opt" class="shrink-0 text-blue-600">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                </li>
            </template>
            <li x-show="filteredOptions.length === 0" class="px-3.5 py-4 text-center text-[11.5px] text-slate-500">
                Wilayah tidak ditemukan
            </li>
        </ul>
    </div>

    @error($name)
        <p class="mt-1.5 flex items-start gap-1 text-[11.5px] font-medium text-rose-600">
            <svg class="mt-0.5 h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>
