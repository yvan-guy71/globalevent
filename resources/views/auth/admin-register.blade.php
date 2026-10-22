<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte admin - Event Global</title>

    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#004aad">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body { background-color: #0f172a; font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .form-input { background-color: rgba(255, 255, 255, 0.05) !important; border: 2px solid rgba(255, 255, 255, 0.1) !important; color: white !important; border-radius: 1rem !important; transition: all 0.3s ease; }
        .form-input:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2) !important; background-color: rgba(255, 255, 255, 0.1) !important; outline: none; }
        .form-input::placeholder { color: rgba(255, 255, 255, 0.35); }
        .submit-btn { background: linear-gradient(135deg, #6366f1, #4f46e5); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 1.25rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4); }
    </style>
</head>
<body class="text-white min-h-screen relative overflow-x-hidden">
    <div class="fixed inset-0 z-0">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&q=80')] bg-cover bg-center opacity-10"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950/50"></div>
    </div>

    <header class="relative z-20 p-6 flex justify-center">
        @include('partials.logo', ['variant' => 'dark'])
    </header>

    <main class="relative z-10 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full glass-card p-8 sm:p-12 rounded-[3rem] shadow-2xl">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-black tracking-tight uppercase">Créer un compte admin</h2>
                <p class="mt-3 text-indigo-100/60 font-medium">Espace réservé à l'administration.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border-l-4 border-red-500 backdrop-blur-xl">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-exclamation-circle text-red-500 mt-1"></i>
                        <div class="text-red-200 text-sm font-bold space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('admin.register.post') }}" method="POST">
                @csrf
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-black text-indigo-100 mb-2 uppercase tracking-widest">Nom complet *</label>
                        <div class="relative group">
                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300/30 group-focus-within:text-indigo-400 transition-colors"></i>
                            <input id="name" name="name" type="text" required class="form-input w-full pl-12 pr-4 py-4" placeholder="Admin" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-xs font-black text-indigo-100 mb-2 uppercase tracking-widest">Email *</label>
                        <div class="relative group">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300/30 group-focus-within:text-indigo-400 transition-colors"></i>
                            <input id="email" name="email" type="email" required class="form-input w-full pl-12 pr-4 py-4" placeholder="admin@eventglobal.test" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-black text-indigo-100 mb-2 uppercase tracking-widest">Mot de passe *</label>
                        <div class="relative group">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300/30 group-focus-within:text-indigo-400 transition-colors"></i>
                            <input id="password" name="password" type="password" required class="form-input w-full pl-12 pr-4 py-4" placeholder="8 caractères minimum">
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-black text-indigo-100 mb-2 uppercase tracking-widest">Confirmation *</label>
                        <div class="relative group">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300/30 group-focus-within:text-indigo-400 transition-colors"></i>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="form-input w-full pl-12 pr-4 py-4" placeholder="••••••••">
                        </div>
                    </div>
                </div>
                <label class="flex items-start gap-3 text-sm text-indigo-100/70 cursor-pointer">
                    <input type="checkbox" name="terms" value="1" required {{ old('terms') ? 'checked' : '' }} class="mt-1 w-5 h-5 rounded-lg border-2 border-white/20 bg-white/5 text-indigo-600 focus:ring-4 focus:ring-indigo-500/20">
                    <span>J'accepte les conditions d'utilisation.</span>
                </label>
                <button type="submit" class="submit-btn w-full py-5 text-white shadow-xl">Créer le compte admin</button>
            </form>

            <div class="mt-10 pt-8 border-t border-white/10 text-center">
                <p class="text-sm text-indigo-100/60">
                    Déjà inscrit ?
                    <a href="{{ route('admin.login') }}" class="font-black text-white hover:text-indigo-400 transition-colors underline underline-offset-4 decoration-indigo-500">
                        Se connecter
                    </a>
                </p>
            </div>
        </div>
    </main>
</body>
</html>