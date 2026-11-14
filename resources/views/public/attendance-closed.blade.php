<x-layouts.guest :title="$title . ' | ' . $meeting->title" max-width="max-w-2xl">
    <div class="mx-auto w-full max-w-md text-center">

        <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
            <div class="h-1 bg-amber-500" aria-hidden="true"></div>

            <div class="px-6 py-8 sm:px-8">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-200">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.1em] text-amber-800 ring-1 ring-inset ring-amber-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500" aria-hidden="true"></span>
                    Formulir Tidak Menerima Respons
                </span>

                <h1 class="mt-2.5 text-[24px] font-extrabold leading-tight tracking-tight text-slate-900">
                    {{ $title }}
                </h1>
                <p class="mx-auto mt-1.5 max-w-xs text-[13px] leading-relaxed text-slate-500">
                    {{ $message }}
                </p>

                <div class="mt-6 space-y-1.5 rounded-xl border border-slate-200 bg-slate-50/80 p-4 text-left text-xs">
                    <p class="text-[13px] font-bold leading-snug text-slate-900">{{ $meeting->title }}</p>
                    <p class="font-medium text-slate-600">
                        {{ $meeting->meeting_date->translatedFormat('l, d F Y') }}
                        <span class="text-slate-300">•</span>
                        {{ $meeting->location }}
                    </p>
                    <p class="text-[11px] text-slate-500">Penyelenggara: {{ $meeting->organizer }}</p>
                </div>

                <p class="mt-4 text-left text-[11px] leading-relaxed text-slate-500">
                    Bila Anda merasa seharusnya dapat mengisi, hubungi panitia kegiatan untuk membuka kembali formulir.
                </p>

                <div class="mt-5">
                    <a
                        href="{{ url('/') }}"
                        class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 text-[13px] font-bold text-slate-700 shadow-sm transition-colors hover:bg-slate-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
