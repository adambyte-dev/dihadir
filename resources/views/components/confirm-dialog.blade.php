<div
    x-data="{
        open: false,
        title: 'Konfirmasi Tindakan',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        variant: 'danger', // 'danger' | 'primary' | 'warning'
        targetForm: null,
        callback: null,

        ask(opts = {}) {
            opts = opts || {};
            const title = opts.title;
            const message = opts.message;
            const confirmText = opts.confirmText || 'Ya, Lanjutkan';
            const cancelText = opts.cancelText || 'Batal';
            const variant = opts.variant || 'danger';
            const form = opts.form || null;
            const onConfirm = opts.onConfirm || null;

            this.title = title || (variant === 'danger' ? 'Konfirmasi Hapus' : 'Konfirmasi Tindakan');
            this.message = message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.confirmText = confirmText;
            this.cancelText = cancelText;
            this.variant = variant;
            this.targetForm = form;
            this.callback = onConfirm;
            this.open = true;
        },

        close() {
            this.open = false;
            this.targetForm = null;
            this.callback = null;
        },

        proceed() {
            const form = this.targetForm;
            const cb = this.callback;
            this.close();

            if (typeof cb === 'function') {
                cb();
            } else if (form) {
                if (typeof form.submit === 'function') {
                    form.submit();
                } else if (typeof form === 'string') {
                    const el = document.querySelector(form);
                    if (el) el.submit();
                }
            }
        }
    }"
    x-init="
        window.$confirmDialog = (opts) => ask(opts);
        window.addEventListener('custom-confirm', (e) => ask(e.detail));
        document.addEventListener('submit', (e) => {
            const form = e.target;
            const msg = form.getAttribute('data-confirm');
            if (msg && !form._confirmed) {
                e.preventDefault();
                const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const confirmText = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
                const variant = form.getAttribute('data-confirm-variant') || 'danger';
                ask({
                    title: title,
                    message: msg,
                    confirmText: confirmText,
                    variant: variant,
                    form: form,
                    onConfirm: () => {
                        form._confirmed = true;
                        form.submit();
                    }
                });
            }
        }, true);
    "
    @keydown.escape.window="if (open) close()"
    class="relative z-[9999]"
>
    {{-- Backdrop & Overlay --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"
        aria-hidden="true"
    ></div>

    {{-- Dialog Box Container --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="fixed inset-0 z-10 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="$id('dialog-title')"
    >
        <div
            @click.away="close()"
            class="relative w-full max-w-md overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xl shadow-slate-950/20"
        >
            {{-- Top Accent Line --}}
            <div
                class="absolute inset-x-0 top-0 h-1"
                :class="{
                    'bg-rose-600': variant === 'danger',
                    'bg-blue-600': variant === 'primary',
                    'bg-amber-500': variant === 'warning'
                }"
                aria-hidden="true"
            ></div>

            <div class="flex items-start gap-4 pt-1">
                {{-- Icon Badge --}}
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                    :class="{
                        'bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200': variant === 'danger',
                        'bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-200': variant === 'primary',
                        'bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-200': variant === 'warning'
                    }"
                >
                    {{-- Danger Icon (Trash/Alert) --}}
                    <template x-if="variant === 'danger'">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </template>

                    {{-- Primary Icon (Question / Duplicate / Info) --}}
                    <template x-if="variant === 'primary'">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </template>

                    {{-- Warning Icon (Exclamation) --}}
                    <template x-if="variant === 'warning'">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </template>
                </div>

                {{-- Content Text --}}
                <div class="flex-1 min-w-0">
                    <h3
                        :id="$id('dialog-title')"
                        class="text-base font-bold text-slate-900 leading-snug"
                        x-text="title"
                    ></h3>
                    <p
                        class="mt-1.5 text-xs text-slate-600 leading-relaxed"
                        x-text="message"
                    ></p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-2">
                <button
                    type="button"
                    @click="close()"
                    class="w-full sm:w-auto inline-flex min-h-[38px] items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-xs font-semibold text-slate-700 shadow-2xs transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400"
                    x-text="cancelText"
                >
                    Batal
                </button>
                <button
                    type="button"
                    @click="proceed()"
                    class="w-full sm:w-auto inline-flex min-h-[38px] items-center justify-center rounded-lg px-4 text-xs font-bold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-1"
                    :class="{
                        'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500': variant === 'danger',
                        'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': variant === 'primary',
                        'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500': variant === 'warning'
                    }"
                    x-text="confirmText"
                >
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>
