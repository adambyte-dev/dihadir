@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-xs text-slate-600">
        {{-- Ringkasan Jumlah Data --}}
        <div>
            <p class="tabular text-[12px] text-slate-500">
                Menampilkan
                <span class="font-bold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</span>
                –
                <span class="font-bold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</span>
                dari
                <span class="font-bold text-slate-800">{{ $paginator->total() }}</span>
                total catatan
            </p>
        </div>

        {{-- Tombol Navigasi Halaman (Clean Slate & Blue Style, ringkas dengan onEachSide) --}}
        <div class="inline-flex items-center gap-1">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span
                    aria-disabled="true"
                    aria-label="Halaman sebelumnya"
                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 text-[11.5px] font-semibold text-slate-400 cursor-not-allowed select-none"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 text-[11.5px] font-semibold text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-sm"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Sebelumnya</span>
                </a>
            @endif

            {{-- Nomor Halaman Ringkas --}}
            @foreach ($elements as $element)
                {{-- Separator Titik-Titik (...) --}}
                @if (is_string($element))
                    <span class="inline-flex h-8 min-w-[32px] items-center justify-center px-2 text-[12px] font-semibold text-slate-400 select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Nomor Halaman Aktif / Link --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                aria-current="page"
                                class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg bg-blue-600 px-2.5 text-[12px] font-bold text-white shadow-sm shadow-blue-600/25 select-none"
                            >
                                {{ $page }}
                            </span>
                        @else
                            <a
                                href="{{ $url }}"
                                class="inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 text-[12px] font-semibold text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-600 hover:border-slate-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-sm"
                            >
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 text-[11.5px] font-semibold text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-sm"
                >
                    <span>Berikutnya</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span
                    aria-disabled="true"
                    aria-label="Halaman berikutnya"
                    class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 text-[11.5px] font-semibold text-slate-400 cursor-not-allowed select-none"
                >
                    <span>Berikutnya</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
