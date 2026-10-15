@extends('layouts.app')

@section('title', 'Contact - Event Global')

@section('content')
    <section class="bg-slate-900 py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-5">Parlons de votre évènement</h1>
            <p class="text-lg text-indigo-100">
                Une question, un partenariat, un problème technique ? Notre équipe vous répond rapidement.
            </p>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-16 grid lg:grid-cols-5 gap-10">
        <aside class="lg:col-span-2 space-y-6">
            @php
                $infos = [
                    ['icon' => 'fa-location-dot', 'title' => 'Adresse', 'value' => 'Lomé, Togo'],
                    ['icon' => 'fa-envelope', 'title' => 'Email', 'value' => 'contact@eventglobal.com'],
                    ['icon' => 'fa-phone', 'title' => 'Téléphone', 'value' => '+228 99 46 25 51'],
                    ['icon' => 'fa-clock', 'title' => 'Disponibilité', 'value' => 'Du lundi au samedi, 8h - 18h'],
                ];
            @endphp

            @foreach($infos as $info)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid {{ $info['icon'] }}"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $info['title'] }}</h2>
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $info['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </aside>

        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                <h2 class="text-xl font-black mb-6">Envoyez-nous un message</h2>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-300 text-sm font-bold flex items-center gap-3">
                        <i class="fa-solid fa-circle-check"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border-l-4 border-rose-500 text-rose-700 dark:text-rose-300 text-sm font-bold">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $inputClass = 'w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors';
                    $labelClass = 'block text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2';
                @endphp

                <form action="{{ route('pages.contact.send') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="{{ $labelClass }}">Nom *</label>
                            <input id="name" type="text" name="name" required value="{{ old('name', auth()->user()->name ?? '') }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label for="email" class="{{ $labelClass }}">Email *</label>
                            <input id="email" type="email" name="email" required value="{{ old('email', auth()->user()->email ?? '') }}" class="{{ $inputClass }}">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="{{ $labelClass }}">Sujet *</label>
                        <input id="subject" type="text" name="subject" required value="{{ old('subject') }}"
                               placeholder="Ex : Partenariat, problème sur mon évènement..."
                               class="{{ $inputClass }}">
                    </div>

                    <div>
                        <label for="message" class="{{ $labelClass }}">Message *</label>
                        <textarea id="message" name="message" rows="6" required minlength="10"
                                  placeholder="Décrivez votre demande..."
                                  class="{{ $inputClass }} resize-y">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
