<div
    id="dihadir-confirm-dialog"
    x-data="{
        open: false,
        title: '',
        message: '',
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        variant: 'danger',
        targetForm: null,
        callback: null,

        ask(opts = {}) {
            if (!opts) return;
            this.title = opts.title || (opts.variant === 'danger' ? 'Konfirmasi Hapus' : 'Konfirmasi Tindakan');
            this.message = opts.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.confirmText = opts.confirmText || 'Ya, Lanjutkan';
            this.cancelText = opts.cancelText || 'Batal';
            this.variant = opts.variant || 'danger';
            this.targetForm = opts.form || null;
            this.callback = opts.onConfirm || null;
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
        window.addEventListener('custom-confirm', (e) => {
            if (e && e.detail) ask(e.detail);
        });
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form && typeof form.getAttribute === 'function' && form.hasAttribute('data-confirm')) {
                const msg = form.getAttribute('data-confirm');
                if (msg && msg.trim() !== '' && !form._confirmed) {
                    e.preventDefault();
                    e.stopPropagation();
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
            }
        }, true);
    "
    @keydown.escape.window="if (open) close()"
    x-cloak
    x-show="open"
    class="relative z-[9999]"
    style="display: none;"
>
    <template x-if="open">
        <div>
            {{-- Backdrop --}}
            <div
                class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity duration-150"
                aria-hidden="true"
                @click="close()"
            ></div>

            {{-- Dialog Container --}}
            <div
                class="fixed inset-0 z-10 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="$id('dialog-title')"
            >
                <div
                    @click.away="close()"
                    class="relative w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-xl"
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
                                'bg-rose-50 text-rose-600 ring-1 ring-rose-200': variant === 'danger',
                                'bg-blue-50 text-blue-600 ring-1 ring-blue-200': variant === 'primary',
                                'bg-amber-50 text-amber-600 ring-1 ring-amber-200': variant === 'warning'
                            }"
                        >
                            {{-- Danger Icon --}}
                            <template x-if="variant === 'danger'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </template>

                            {{-- Primary Icon --}}
                            <template x-if="variant === 'primary'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </template>

                            {{-- Warning Icon --}}
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
    </template>
</div>
