{{-- Scroll To Top Button (Muncul otomatis saat pengguna menggulir halaman ke bawah) --}}
<div
    x-data="{
        showBackToTop: false,
        checkScroll() {
            this.showBackToTop = (window.pageYOffset || document.documentElement.scrollTop) > 320;
        },
        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }"
    x-init="window.addEventListener('scroll', () => checkScroll(), { passive: true }); checkScroll();"
    class="fixed bottom-6 right-6 z-40"
>
    <button
        type="button"
        x-show="showBackToTop"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-90"
        @click="scrollToTop()"
        class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200/90 bg-white/95 text-slate-700 shadow-md backdrop-blur-sm transition-all hover:border-blue-400 hover:bg-blue-600 hover:text-white hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        aria-label="Kembali ke atas halaman"
        title="Kembali ke atas"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 15l7-7 7 7"/>
        </svg>
    </button>
</div>