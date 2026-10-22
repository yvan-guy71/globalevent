<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>Scanner - {{ $event->title }} - Event Global</title>

    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config = { darkMode: 'class' };</script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

    @include('partials.dark-mode-styles')
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 min-h-screen">
    <header class="bg-slate-900 text-white px-4 py-5">
        <div class="max-w-2xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('images/logo.png') }}" alt="Event Global" class="h-9 w-auto shrink-0">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-300/80">Contrôle des accès</p>
                    <p class="font-black truncate">{{ $event->title }}</p>
                </div>
            </div>
            <span class="text-xs font-black uppercase tracking-wider bg-indigo-600 px-3 py-1.5 rounded-full shrink-0">
                Sécurité
            </span>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8 space-y-8">
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-5 text-center">
                <div class="text-2xl font-black">{{ $stats['total'] }}</div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Billets</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-5 text-center">
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['used'] }}</div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Entrés</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-5 text-center">
                <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $stats['remaining'] }}</div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">En attente</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
            <h1 class="text-lg font-black mb-4 flex items-center gap-3">
                <i class="fa-solid fa-qrcode text-indigo-600 dark:text-indigo-400"></i>
                Scanner un billet
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Saisissez ou scannez le code du billet (ex : TKT-XXXXXXXXXX).</p>

            <form id="checkin-form" class="space-y-4">
                @csrf
                <input type="text" name="code" id="checkin-code" required autocomplete="off"
                       placeholder="TKT-XXXXXXXXXX"
                       class="w-full px-4 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-lg font-mono font-bold uppercase tracking-wider text-center focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-check"></i> Valider
                    </button>
                    <button type="button" id="camera-btn"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl hover:opacity-90 transition-opacity">
                        <i class="fa-solid fa-camera"></i> Caméra
                    </button>
                </div>
            </form>

            <div id="scan-result" class="hidden mt-4 p-4 rounded-2xl text-sm font-bold"></div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
            <h2 class="text-lg font-black mb-6 flex items-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-indigo-600 dark:text-indigo-400"></i>
                Derniers passages
            </h2>

            @forelse($recent as $ticket)
                <div class="flex items-center justify-between gap-4 py-3 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-800' : '' }}">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $ticket->holder() }}</p>
                        <p class="text-xs font-mono text-slate-400">{{ $ticket->code }} &middot; {{ $ticket->ticketType?->name }}</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 shrink-0">
                        {{ $ticket->checked_in_at?->format('H:i') }}
                    </span>
                </div>
            @empty
                <p class="text-slate-500 dark:text-slate-400">Aucun passage enregistré pour l'instant.</p>
            @endforelse
        </div>

        <p class="text-center text-xs text-slate-400 dark:text-slate-600">
            Lien de scan sécurisé pour <strong>{{ $event->title }}</strong> &middot; Event Global
        </p>
    </main>

    <script>
        (function () {
            const form = document.getElementById('checkin-form');
            const codeInput = document.getElementById('checkin-code');
            const resultEl = document.getElementById('scan-result');
            const cameraBtn = document.getElementById('camera-btn');
            const endpoint = @json(route('scan.process', ['event' => $event->slug, 'token' => $token]));
            const token = document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('#checkin-form input[name="_token"]')?.value;

            function show(status, message) {
                resultEl.classList.remove('hidden');
                resultEl.className = 'mt-4 p-4 rounded-2xl text-sm font-bold ' + (
                    status === 'valid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                    status === 'used' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                    'bg-rose-50 text-rose-700 border border-rose-200'
                );
                resultEl.textContent = message;
                if (status === 'valid') {
                    resultEl.insertAdjacentHTML('beforeend', ' <i class="fa-solid fa-circle-check ml-1"></i>');
                }
            }

            async function check(code) {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ code }),
                });

                const data = await res.json();
                show(data.status, data.message);

                if (data.status === 'valid') {
                    codeInput.value = '';
                    setTimeout(() => codeInput.focus(), 100);
                }
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const code = codeInput.value.trim();
                    if (code) check(code);
                });
            }

            if (cameraBtn && navigator.mediaDevices?.getUserMedia) {
                cameraBtn.addEventListener('click', async function () {
                    let stream;
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                    } catch (err) {
                        show('invalid', 'Caméra indisponible : autorisez l\'accès ou utilisez le scan manuel.');
                        return;
                    }

                    const video = document.createElement('video');
                    video.style.position = 'fixed';
                    video.style.inset = '0';
                    video.style.width = '100%';
                    video.style.height = '100%';
                    video.style.zIndex = '9999';
                    video.style.background = '#000';
                    video.setAttribute('playsinline', '');
                    document.body.appendChild(video);
                    video.srcObject = stream;
                    await video.play();

                    let stopped = false;

                    const stopHandler = () => {
                        if (stopped) return;
                        stopped = true;
                        stream.getTracks().forEach(t => t.stop());
                        video.remove();
                    };

                    video.addEventListener('click', stopHandler);

                    if ('BarcodeDetector' in window) {
                        const detector = new window.BarcodeDetector({ formats: ['qr_code'] });

                        async function detect() {
                            if (stopped) return;
                            try {
                                const codes = await detector.detect(video);
                                if (codes.length) {
                                    const raw = codes[0].rawValue || '';
                                    stopped = true;
                                    stream.getTracks().forEach(t => t.stop());
                                    video.remove();
                                    check(raw);
                                    return;
                                }
                            } catch (e) { /* ignore frame errors */ }
                            requestAnimationFrame(detect);
                        }

                        requestAnimationFrame(detect);
                    } else if (window.jsQR) {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        function detect() {
                            if (stopped) return;
                            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                const result = window.jsQR(imageData.data, imageData.width, imageData.height);
                                if (result && result.data) {
                                    stopped = true;
                                    stream.getTracks().forEach(t => t.stop());
                                    video.remove();
                                    check(result.data);
                                    return;
                                }
                            }
                            requestAnimationFrame(detect);
                        }

                        requestAnimationFrame(detect);
                    } else {
                        show('invalid', 'Lecture caméra indisponible. Utilisez le scan manuel.');
                        stopHandler();
                    }
                });
            } else if (cameraBtn) {
                cameraBtn.addEventListener('click', function () {
                    show('invalid', 'Caméra indisponible. Utilisez le scan manuel.');
                });
            }
        })();
    </script>
</body>
</html>
