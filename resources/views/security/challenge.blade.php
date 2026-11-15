<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Memeriksa Keamanan Peramban | DIHADIR</title>

    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        @keyframes pulseRing {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .pulse-ring { animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin-slow { animation: spinSlow 3s linear infinite; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen flex flex-col justify-between antialiased selection:bg-[#3B82F6] selection:text-white">

    <!-- Header Mini -->
    <header class="w-full border-b border-[#CBD5E1]/60 bg-white/80 backdrop-blur-sm py-4 px-6 sm:px-10">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-dihadir-icon.png') }}" alt="DIHADIR" class="w-8 h-8 rounded-lg object-contain">
                <div>
                    <span class="font-bold text-slate-800 tracking-tight text-base">DIHADIR</span>
                    <span class="text-xs text-slate-400 block -mt-0.5">SMKN 1 Majalengka</span>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Pemeriksaan Keamanan Aktif</span>
            </div>
        </div>
    </header>

    <!-- Main Challenge Card -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-200/80 shadow-xl shadow-slate-200/50 p-6 sm:p-10 text-center relative overflow-hidden">
            
            <!-- Aksen Garis Atas -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-sky-400"></div>

            <!-- Animasi Radar / Shield Keamanan Cloudflare Style -->
            <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full bg-blue-100 pulse-ring"></div>
                <div class="w-20 h-20 rounded-full border-2 border-dashed border-blue-400 spin-slow absolute"></div>
                <div class="relative w-16 h-16 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight mb-2">
                Memeriksa Keamanan Peramban Anda
            </h1>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">
                Mohon tunggu beberapa detik. Sistem sedang memverifikasi koneksi Anda sebelum masuk ke <strong class="text-slate-700">DIHADIR</strong> demi melindungi data absensi dari aktivitas bot otomatis.
            </p>

            <!-- Box Status Verifikasi Interaktif -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 flex items-center justify-between text-left">
                <div class="flex items-center gap-3">
                    <div id="statusIcon" class="w-6 h-6 flex items-center justify-center text-blue-600">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                    <div>
                        <div id="statusText" class="text-sm font-semibold text-slate-700">Memeriksa peramban...</div>
                        <div id="subStatusText" class="text-xs text-slate-400">Proses berlangsung otomatis</div>
                    </div>
                </div>

                <div class="text-right">
                    <span id="countdown" class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-200">
                        3s
                    </span>
                </div>
            </div>

            <!-- Footer Card Keamanan -->
            <div class="text-xs text-slate-400 space-y-1.5 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-center gap-2">
                    <span>Ray ID: <code class="font-mono text-slate-600 font-medium">{{ $rayId }}</code></span>
                    <span>•</span>
                    <span>IP Anda: <code class="font-mono text-slate-600 font-medium">{{ $clientIp }}</code></span>
                </div>
                <div>Perlindungan Akses SMKN 1 Majalengka & Cloudflare Shield</div>
            </div>
        </div>
    </main>

    <!-- Footer Bawah -->
    <footer class="w-full text-center py-4 text-xs text-slate-400">
        &copy; {{ date('Y') }} DIHADIR — SMKN 1 Majalengka. Hak cipta dilindungi.
    </footer>

    <script>
        (function() {
            var targetUrl = @json($target);
            var token = @json(\App\Http\Controllers\SecurityChallengeController::generateToken($clientIp));
            var countdownEl = document.getElementById('countdown');
            var statusText = document.getElementById('statusText');
            var subStatusText = document.getElementById('subStatusText');
            var statusIcon = document.getElementById('statusIcon');
            var seconds = 3;

            var timer = setInterval(function() {
                seconds--;
                if (seconds > 0) {
                    countdownEl.textContent = seconds + 's';
                } else {
                    clearInterval(timer);
                    countdownEl.textContent = 'OK';
                    statusText.textContent = 'Verifikasi Berhasil!';
                    subStatusText.textContent = 'Mengarahkan ke halaman tujuan...';
                    statusIcon.innerHTML = '<svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';

                    // Kirim request verifikasi dan peroleh cookie clearance
                    fetch('/cdn-cgi/verify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            token: token,
                            r: targetUrl
                        })
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.success) {
                            window.location.replace(data.redirect || targetUrl);
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(function(err) {
                        window.location.replace(targetUrl);
                    });
                }
            }, 1000);
        })();
    </script>
</body>
</html>
