@props([
    'name' => 'signature',
    'label' => 'Tanda Tangan Digital',
    'required' => true,
    'error' => null,
])

<div
    x-data="{
        isDrawing: false,
        hasDrawn: false,
        signatureData: '',
        canvas: null,
        ctx: null,

        init() {
            this.canvas = this.$refs.canvas;
            this.ctx = this.canvas.getContext('2d');
            this.resizeCanvas();
            window.addEventListener('resize', () => this.resizeCanvas());
        },

        resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const rect = this.canvas.getBoundingClientRect();

            const currentImg = this.hasDrawn ? this.canvas.toDataURL() : null;

            this.canvas.width = rect.width * ratio;
            this.canvas.height = 200 * ratio;
            this.ctx.scale(ratio, ratio);

            this.ctx.lineWidth = 2.5;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            this.ctx.strokeStyle = '#0F172A';

            if (currentImg) {
                const img = new Image();
                img.onload = () => {
                    this.ctx.drawImage(img, 0, 0, rect.width, 200);
                };
                img.src = currentImg;
            }
        },

        getCoordinates(e) {
            const rect = this.canvas.getBoundingClientRect();
            const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        },

        startDrawing(e) {
            e.preventDefault();
            this.isDrawing = true;
            this.hasDrawn = true;
            const pos = this.getCoordinates(e);
            this.ctx.beginPath();
            this.ctx.moveTo(pos.x, pos.y);
        },

        draw(e) {
            if (!this.isDrawing) return;
            e.preventDefault();
            const pos = this.getCoordinates(e);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
        },

        stopDrawing() {
            if (!this.isDrawing) return;
            this.isDrawing = false;
            this.signatureData = this.canvas.toDataURL('image/png');
        },

        clear() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            this.ctx.clearRect(0, 0, this.canvas.width / ratio, this.canvas.height / ratio);
            this.hasDrawn = false;
            this.signatureData = '';
        }
    }"
    class="w-full select-none"
>
    <div class="mb-1.5 flex items-end justify-between gap-3">
        <label class="block text-[13px] font-semibold text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="font-bold text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(wajib diisi)</span>
            @endif
        </label>

        <button
            type="button"
            @click="clear()"
            x-show="hasDrawn"
            x-cloak
            class="inline-flex min-h-[28px] items-center gap-1 rounded-md border border-slate-200 bg-white px-2 text-[11px] font-semibold text-slate-600 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-1"
        >
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Hapus</span>
        </button>
    </div>

    {{-- Penampung Base64 PNG --}}
    <input type="hidden" name="{{ $name }}" :value="signatureData" required>

    <div class="relative h-[200px] w-full touch-none overflow-hidden rounded-xl border bg-slate-50/80 shadow-inner transition-colors @error($name) border-rose-400 ring-2 ring-rose-500/20 @else border-slate-300 @enderror">
        <div
            x-show="!hasDrawn"
            x-cloak
            class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center p-4 text-center transition-opacity"
        >
            <span class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-500 ring-1 ring-inset ring-slate-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </span>
            <p class="text-[12.5px] font-semibold text-slate-600">Bubuhkan tanda tangan di area ini</p>
            <p class="mt-0.5 text-[11px] text-slate-500">Gunakan ujung jari pada layar HP, atau klik &amp; geser mouse pada laptop.</p>
        </div>

        <canvas
            x-ref="canvas"
            class="relative z-10 h-full w-full cursor-crosshair"
            @mousedown="startDrawing($event)"
            @mousemove="draw($event)"
            @mouseup="stopDrawing()"
            @mouseleave="stopDrawing()"
            @touchstart="startDrawing($event)"
            @touchmove="draw($event)"
            @touchend="stopDrawing()"
        ></canvas>

        <div class="pointer-events-none absolute bottom-6 left-8 right-8 z-0 border-b border-dashed border-slate-300/80" aria-hidden="true"></div>
    </div>

    @error($name)
        <p class="mt-1.5 flex items-start gap-1 text-[11.5px] font-medium text-rose-600">
            <svg class="mt-0.5 h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $message }}</span>
        </p>
    @else
        <p class="mt-1.5 text-[11px] text-slate-500">Tanda tangan disimpan sebagai bukti kehadiran fisik pertemuan.</p>
    @enderror
</div>
