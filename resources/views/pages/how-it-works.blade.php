@extends('layouts.app')

@section('title', 'Comment ça marche - Event Global')

@section('content')
    <section class="bg-slate-900 py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-6">
                <i class="fa-solid fa-circle-question text-indigo-300"></i> Guide du créateur
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-5">Comment ça marche ?</h1>
            <p class="text-lg text-indigo-100">
                Event Global vous permet de publier un concert, une compétition, un festival ou toute autre
                manifestation en quelques minutes — et de la rendre visible auprès de milliers de personnes.
            </p>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-16">
        @php
            $steps = [
                ['icon' => 'fa-user-plus', 'title' => 'Créez votre compte', 'text' => 'Une inscription gratuite suffit. Vous devenez organisateur immédiatement, sans validation préalable.'],
                ['icon' => 'fa-pen-to-square', 'title' => 'Décrivez l\'évènement', 'text' => 'Titre, type, date, lieu, tarif, capacité, affiche... tout ce dont votre public a besoin pour se décider.'],
                ['icon' => 'fa-check-double', 'title' => 'Publiez ou gardez en brouillon', 'text' => 'Enregistrez en brouillon le temps de finaliser, puis publiez d\'un clic. Vous pouvez modifier à tout moment.'],
                ['icon' => 'fa-bullhorn', 'title' => 'Partagez', 'text' => 'Chaque évènement possède sa page publique avec un bouton de partage prêt pour vos réseaux sociaux.'],
            ];
        @endphp

        <div class="grid md:grid-cols-2 gap-8">
            @foreach($steps as $i => $step)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 reveal reveal-up" style="transition-delay: {{ $i * 100 }}ms">
                    <div class="flex items-start gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid {{ $step['icon'] }}"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black mb-2">{{ $i + 1 }}. {{ $step['title'] }}</h2>
                            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">{{ $step['text'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 bg-slate-900 rounded-3xl p-10 md:p-14 reveal reveal-up">
            <h2 class="text-2xl md:text-3xl font-black text-white mb-8 text-center">Questions fréquentes</h2>
            <div class="grid md:grid-cols-2 gap-8">
                @php
                    $faqs = [
                        ['q' => 'Est-ce gratuit ?', 'a' => 'Oui. La création de compte et la publication d\'évènements sont totalement gratuites.'],
                        ['q' => 'Puis-je modifier un évènement publié ?', 'a' => 'À tout moment, depuis « Mes évènements » : modifiez les informations ou passez le statut en annulé.'],
                        ['q' => 'Comment ajouter mon affiche ?', 'a' => 'Lors de la création ou de la modification, importez une image (jpg, png ou webp, 4 Mo maximum).'],
                        ['q' => 'Puis-je proposer un évènement gratuit ?', 'a' => 'Bien sûr : laissez le prix à 0, l\'évènement sera automatiquement marqué comme « Gratuit ».'],
                    ];
                @endphp
                @foreach($faqs as $faq)
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                        <h3 class="font-black text-white mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-circle-dot text-indigo-400 text-xs"></i>{{ $faq['q'] }}
                        </h3>
                        <p class="text-indigo-100/70 text-sm leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                @auth
                    <a href="{{ route('events.create') }}" class="inline-block px-8 py-4 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition-colors">
                        Créer un évènement maintenant
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white text-indigo-600 font-black rounded-2xl hover:bg-slate-100 transition-colors">
                        Créer mon compte gratuitement
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection
