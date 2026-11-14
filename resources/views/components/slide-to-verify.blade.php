@props([
    'name' => 'slider_token',
    'endpoint' => null,
    'checkEndpoint' => null,
])

@php
    $challengeUrl = $endpoint ?? route('puzzle.challenge');
    $checkUrl = $checkEndpoint ?? route('puzzle.check');
@endphp

<div
    x-data="{
        status: 'memuat', // 'memuat' | 'siap' | 'gagal'
        hasil: 'netral',  // 'netral' | 'cocok' | 'meleset'
        posisi: 0,
        batas: 0,
        tantangan: null,
        sliderToken: '',
        menyeret: false,

        init() {
            this.muatTantangan();
        },

        dataUrl(svg) {
            if (!svg) return '';
            return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
        },

        async muatTantangan() {
            this.status = 'memuat';
            this.hasil = 'netral';
            this.posisi = 0;
            this.sliderToken = '';
            this.$dispatch('verified', false);

            try {
                const res = await fetch('{{ $challengeUrl }}', {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                if (!data || !data.token || !data.svg || !data.potonganSvg) {
                    throw new Error('Format puzzle tidak valid');
                }
                this.tantangan = data;
                this.batas = data.lebar - data.potongan;
                this.status = 'siap';
            } catch (err) {
                console.error('Puzzle load error:', err);
                this.status = 'gagal';
            }
        },

        buatPayload(p) {
            if (!this.tantangan) return '';
            return 'pz1.' + this.tantangan.token + '.' + Math.round(p);
        },

        geser(p) {
            if (!this.tantangan) return;
            this.posisi = Math.max(0, Math.min(this.batas, Math.round(p)));
            // Setelah cocok, jika digeser lagi maka reset status verifikasi
            if (this.hasil === 'cocok') {
                this.hasil = 'netral';
                this.sliderToken = '';
                this.$dispatch('verified', false);
            }
        },

        dariPointer(ev) {
            const el = this.$refs.puzzleCanvas;
            if (!el || !this.tantangan) return;
            const rect = el.getBoundingClientRect();
            if (!rect || rect.width <= 0) return;
            const clientX = ev.clientX ?? (ev.touches && ev.touches[0] ? ev.touches[0].clientX : 0);
            const ratio = (clientX - rect.left) / rect.width;
            this.geser(ratio * this.batas);
        },

        onPointerDown(ev) {
            if (this.hasil === 'cocok' || !this.tantangan) return;
            if (ev.currentTarget && ev.currentTarget.setPointerCapture) {
                try {
                    ev.currentTarget.setPointerCapture(ev.pointerId);
                } catch (e) {}
            }
            this.menyeret = true;
            this.dariPointer(ev);
        },

        onPointerMove(ev) {
            if (!this.menyeret) return;
            this.dariPointer(ev);
        },

        onPointerUp(ev) {
            if (!this.menyeret) return;
            this.menyeret = false;
            if (ev.currentTarget && ev.currentTarget.releasePointerCapture) {
                try {
                    ev.currentTarget.releasePointerCapture(ev.pointerId);
                } catch (e) {}
            }
            this.periksa();
        },

        async periksa() {
            if (!this.tantangan) return;
            const payload = this.buatPayload(this.posisi);

            try {
                const res = await fetch('{{ $checkUrl }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ captcha: payload }),
                    cache: 'no-store'
                });
                const body = await res.json().catch(() => null);
                if (body && body.cocok) {
                    this.hasil = 'cocok';
                    this.sliderToken = payload;
                    this.$dispatch('verified', true);
                } else {
                    this.hasil = 'meleset';
                    this.sliderToken = '';
                    this.$dispatch('verified', false);
                }
            } catch (err) {
                console.error('Puzzle check error:', err);
                this.hasil = 'netral';
                this.sliderToken = '';
                this.$dispatch('verified', false);
            }
        }
    }"
    class="w-full select-none"
>
    <div class="mb-1.5 flex items-center justify-between gap-2">
        <label class="block text-[13px] font-semibold text-slate-700">
            Verifikasi Keamanan (Puzzle Geser)
            <span class="font-bold text-rose-600" aria-hidden="true">*</span>
        </label>
        <span class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-500">Geser Potongan</span>
    </div>

    {{-- Input hidden yang dikirim saat submit formulir --}}
    <input type="hidden" name="{{ $name }}" :value="sliderToken">

    {{-- State 1: Memuat --}}
    <div
        x-show="status === 'memuat'"
        class="flex min-h-[52px] items-center justify-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-xs text-slate-500 shadow-sm"
    >
        <svg class="h-4 w-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span>Menyiapkan puzzle verifikasi mandiri...</span>
    </div>

    {{-- State 2: Gagal --}}
    <div
        x-show="status === 'gagal'"
        x-cloak
        class="flex min-h-[52px] items-center justify-between gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-700 shadow-sm"
    >
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Gagal memuat puzzle. Silakan muat ulang.</span>
        </div>
        <button
            type="button"
            @click="muatTantangan()"
            class="font-bold underline transition-colors hover:text-rose-900 cursor-pointer"
        >
            Coba Lagi
        </button>
    </div>

    {{-- State 3: Siap & Interaktif --}}
    <div
        x-show="status === 'siap' && tantangan"
        x-cloak
        class="overflow-hidden rounded-xl border bg-white p-3 shadow-sm transition-all"
        :class="hasil === 'cocok'
            ? 'border-emerald-500 bg-emerald-50/20 ring-1 ring-emerald-500/30'
            : (hasil === 'meleset' ? 'border-rose-400 bg-rose-50/10' : 'border-slate-200 hover:border-slate-300')"
    >
        <p class="mb-2 text-[11px] font-semibold text-slate-600">
            Geser potongan puzzle hingga menutupi lubang pada gambar:
        </p>

        {{-- Wadah Kanvas Gambar Puzzle --}}
        <div
            x-ref="puzzleCanvas"
            class="relative w-full overflow-hidden rounded-lg bg-slate-900 shadow-inner select-none touch-none cursor-grab active:cursor-grabbing"
            :style="`aspect-ratio: ${tantangan ? (tantangan.lebar + ' / ' + tantangan.tinggi) : '640 / 360'};`"
            @pointerdown="onPointerDown($event)"
            @pointermove="onPointerMove($event)"
            @pointerup="onPointerUp($event)"
            @pointercancel="onPointerUp($event)"
        >
            {{-- Gambar Utama (Background berserta Lubang Target) --}}
            <template x-if="tantangan && tantangan.svg">
                <img
                    :src="dataUrl(tantangan.svg)"
                    alt="Puzzle Background"
                    class="absolute inset-0 h-full w-full object-fill pointer-events-none select-none"
                    draggable="false"
                >
            </template>

            {{-- Potongan Puzzle yang Digeser --}}
            <template x-if="tantangan && tantangan.potonganSvg">
                <img
                    :src="dataUrl(tantangan.potonganSvg)"
                    alt="Potongan Puzzle"
                    aria-hidden="true"
                    draggable="false"
                    class="absolute rounded-[14%] shadow-lg pointer-events-none transition-shadow"
                    :class="hasil === 'cocok' ? 'ring-2 ring-emerald-400' : 'ring-1 ring-white/90'"
                    :style="`left: ${(posisi / tantangan.lebar) * 100}%; top: ${tantangan.y * 100}%; width: ${(tantangan.potongan / tantangan.lebar) * 100}%; height: ${(tantangan.potongan / tantangan.tinggi) * 100}%;`"
                >
            </template>
        </div>

        {{-- Slider Kontrol Penggeser --}}
        <div class="mt-3">
            <input
                type="range"
                min="0"
                :max="batas"
                step="1"
                :value="posisi"
                @input="geser(Number($event.target.value))"
                @change="periksa()"
                @pointerup="periksa()"
                :disabled="hasil === 'cocok'"
                aria-label="Geser potongan puzzle"
                class="w-full cursor-pointer accent-blue-600 disabled:cursor-not-allowed"
            >
        </div>

        {{-- Keterangan Umpan Balik --}}
        <div class="mt-2 flex items-center justify-between text-[11px]">
            <div>
                <template x-if="hasil === 'cocok'">
                    <span class="inline-flex items-center gap-1.5 font-bold text-emerald-600">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Cocok! Silakan tekan tombol masuk.
                    </span>
                </template>

                <template x-if="hasil === 'meleset'">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-rose-600">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Posisi belum pas. Geser kembali secara tepat.
                    </span>
                </template>

                <template x-if="hasil === 'netral'">
                    <span class="text-slate-500">
                        Lepaskan slider saat posisi sudah sejajar.
                    </span>
                </template>
            </div>

            <button
                type="button"
                @click="muatTantangan()"
                class="inline-flex items-center gap-1 font-semibold text-blue-600 transition-colors hover:text-blue-800 cursor-pointer"
                title="Ganti gambar puzzle baru"
            >
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Acak Gambar</span>
            </button>
        </div>
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
