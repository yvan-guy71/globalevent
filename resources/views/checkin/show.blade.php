@extends('layouts.app')

@section('title', 'Check-in - '.$event->title)

@section('content')
    <section class="bg-slate-900 pt-16 pb-24 px-4">
        <div class="max-w-5xl mx-auto">
            <nav class="text-xs font-bold uppercase tracking-widest text-indigo-300/80 mb-4">
                <a href="{{ route('events.show', $event) }}" class="hover:text-white">Évènement</a>
                <span class="mx-2">/</span>
                <span class="text-white">Check-in</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Contrôle des accès</h1>
                    <p class="text-indigo-100">{{ $event->title }}</p>
                </div>
                <a href="{{ route('ticket-types.index', $event) }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-slate-900 font-black rounded-2xl hover:bg-slate-100 transition-colors shrink-0">
                    <i class="fa-solid fa-cog"></i> Gérer les billets
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 -mt-12 relative z-10 pb-20 space-y-8">
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 text-center">
                <div class="text-3xl font-black">{{ $stats['total'] }}</div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Billets</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 text-center">
                <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['used'] }}</div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">Entrés</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 text-center">
                <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $stats['remaining'] }}</div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-1">En attente</div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
                <h2 class="text-lg font-black mb-4 flex items-center gap-3">
                    <i class="fa-solid fa-keyboard text-indigo-600 dark:text-indigo-400"></i>
                    Scanner manuel
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Saisissez ou scannez le code du billet (ex : TKT-XXXXXXXXXX).</p>

                <form action="{{ route('checkin.process', $event) }}" method="POST" id="checkin-form" class="space-y-4">
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

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
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
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
            <h2 class="text-lg font-black mb-2 flex items-center gap-3">
                <i class="fa-solid fa-share-nodes text-indigo-600 dark:text-indigo-400"></i>
                Partager le scanner à votre équipe
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Générez un lien pour que vos agents de sécurité puissent scanner les billets
                <strong>sans créer de compte</strong>. Le lien ne donne accès qu'au contrôle des billets
                de cet évènement — et peut être révoqué à tout moment.
            </p>

            @if($event->hasScanShare())
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" id="scan-share-url" readonly value="{{ $event->scanUrl() }}"
                               class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-mono text-slate-600 dark:text-slate-300">
                        <button type="button" id="copy-share-url"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors shrink-0">
                            <i class="fa-regular fa-copy"></i> Copier le lien
                        </button>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ $event->scanUrl() }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl hover:opacity-90 transition-opacity">
                            <i class="fa-solid fa-up-right-from-square"></i> Ouvrir le scanner partagé
                        </a>
                        <form action="{{ route('checkin.unshare', $event) }}" method="POST"
                              onsubmit="return confirm('Révoquer le lien ? Les agents équipés ne pourront plus scanner.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-black rounded-2xl hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors w-full">
                                <i class="fa-solid fa-ban"></i> Révoquer le lien
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <form action="{{ route('checkin.share', $event) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-link"></i> Générer un lien de scan
                    </button>
                </form>
            @endif
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                const form = document.getElementById('checkin-form');
                const codeInput = document.getElementById('checkin-code');
                const resultEl = document.getElementById('scan-result');
                const cameraBtn = document.getElementById('camera-btn');
                const endpoint = @json(route('checkin.process', $event));
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

                if (cameraBtn && 'BarcodeDetector' in window && navigator.mediaDevices?.getUserMedia) {
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

                        const detector = new window.BarcodeDetector({ formats: ['qr_code'] });
                        let stopped = false;

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

                        const stopHandler = () => {
                            if (stopped) return;
                            stopped = true;
                            stream.getTracks().forEach(t => t.stop());
                            video.remove();
                        };

                        video.addEventListener('click', stopHandler);
                        requestAnimationFrame(detect);
                    });
                } else if (cameraBtn) {
                    cameraBtn.addEventListener('click', function () {
                        show('invalid', 'La lecture par caméra n\'est pas supportée sur ce navigateur. Utilisez le scan manuel.');
                    });
                }

                const copyBtn = document.getElementById('copy-share-url');
                const shareInput = document.getElementById('scan-share-url');
                if (copyBtn && shareInput) {
                    copyBtn.addEventListener('click', function () {
                        const done = () => {
                            copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Copié !';
                            setTimeout(() => {
                                copyBtn.innerHTML = '<i class="fa-regular fa-copy"></i> Copier le lien';
                            }, 2000);
                        };
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(shareInput.value).then(done).catch(() => {
                                shareInput.select();
                                document.execCommand('copy');
                                done();
                            });
                        } else {
                            shareInput.select();
                            document.execCommand('copy');
                            done();
                        }
                    });
                }
            })();
        </script>
    @endpush
@endsection
