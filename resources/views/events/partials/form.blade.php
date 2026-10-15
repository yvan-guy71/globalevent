@php
    $event = $event ?? null;
    $inputClass = 'w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors';
    $labelClass = 'block text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2';
    $errorClass = 'text-xs font-bold text-rose-600 mt-1';
@endphp

<div class="space-y-8">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
        <h2 class="text-lg font-black mb-6 flex items-center gap-3">
            <i class="fa-solid fa-circle-info text-indigo-600 dark:text-indigo-400"></i>
            Informations principales
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="title" class="{{ $labelClass }}">Titre de l'évènement *</label>
                <input type="text" id="title" name="title" required maxlength="120"
                       value="{{ old('title', $event->title ?? '') }}"
                       placeholder="Ex : Nuit du Mawazo - Concert live"
                       class="{{ $inputClass }}">
                @error('title') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="{{ $labelClass }}">Type d'évènement *</label>
                <select id="type" name="type" required class="{{ $inputClass }}">
                    <option value="">Choisir un type</option>
                    @foreach($types as $slug => $type)
                        <option value="{{ $slug }}" {{ old('type', $event->type ?? '') === $slug ? 'selected' : '' }}>
                            {{ $type['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('type') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="{{ $labelClass }}">Statut</label>
                <select id="status" name="status" class="{{ $inputClass }}">
                    @foreach(config('events.statuses') as $slug => $label)
                        <option value="{{ $slug }}" {{ old('status', $event->status ?? 'published') === $slug ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="{{ $labelClass }}">Description *</label>
                <textarea id="description" name="description" rows="6" required minlength="20"
                          placeholder="Présentez votre évènement : programme, artistes, ambiance..."
                          class="{{ $inputClass }} resize-y">{{ old('description', $event->description ?? '') }}</textarea>
                @error('description') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
        <h2 class="text-lg font-black mb-6 flex items-center gap-3">
            <i class="fa-solid fa-calendar-days text-indigo-600 dark:text-indigo-400"></i>
            Date et lieu
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label for="starts_at" class="{{ $labelClass }}">Début *</label>
                <input type="datetime-local" id="starts_at" name="starts_at" required
                       value="{{ old('starts_at', isset($event) && $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '') }}"
                       class="{{ $inputClass }}">
                @error('starts_at') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ends_at" class="{{ $labelClass }}">Fin (optionnel)</label>
                <input type="datetime-local" id="ends_at" name="ends_at"
                       value="{{ old('ends_at', isset($event) && $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '') }}"
                       class="{{ $inputClass }}">
                @error('ends_at') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="venue" class="{{ $labelClass }}">Lieu / Salle *</label>
                <input type="text" id="venue" name="venue" required maxlength="150"
                       value="{{ old('venue', $event->venue ?? '') }}"
                       placeholder="Ex : Palais des Congrès"
                       class="{{ $inputClass }}">
                @error('venue') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="city" class="{{ $labelClass }}">Ville *</label>
                <select id="city" name="city" required class="{{ $inputClass }}">
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ old('city', $event->city ?? 'Lomé') === $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
                @error('city') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="country" class="{{ $labelClass }}">Pays *</label>
                <select id="country" name="country" required class="{{ $inputClass }}">
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ old('country', $event->country ?? 'Togo') === $country ? 'selected' : '' }}>
                            {{ $country }}
                        </option>
                    @endforeach
                </select>
                @error('country') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="{{ $labelClass }}">Adresse précise (optionnel)</label>
                <input type="text" id="address" name="address" maxlength="180"
                       value="{{ old('address', $event->address ?? '') }}"
                       placeholder="Ex : Boulevard du 13 Janvier, derrière la poste"
                       class="{{ $inputClass }}">
                @error('address') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
        <h2 class="text-lg font-black mb-6 flex items-center gap-3">
            <i class="fa-solid fa-ticket text-indigo-600 dark:text-indigo-400"></i>
            Tarifs et capacité
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <label for="price" class="{{ $labelClass }}">Prix (0 = gratuit)</label>
                <input type="number" id="price" name="price" min="0" step="100"
                       value="{{ old('price', $event->price ?? 0) }}"
                       class="{{ $inputClass }}">
                @error('price') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="currency" class="{{ $labelClass }}">Devise</label>
                <select id="currency" name="currency" class="{{ $inputClass }}">
                    @foreach($currencies as $code => $label)
                        <option value="{{ $code }}" {{ old('currency', $event->currency ?? 'FCFA') === $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('currency') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="capacity" class="{{ $labelClass }}">Capacité (optionnel)</label>
                <input type="number" id="capacity" name="capacity" min="1"
                       value="{{ old('capacity', $event->capacity ?? '') }}"
                       placeholder="Ex : 500"
                       class="{{ $inputClass }}">
                @error('capacity') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <label class="md:col-span-3 flex items-center gap-3 text-sm font-bold text-slate-600 dark:text-slate-300 cursor-pointer">
                <input type="checkbox" id="is_free" name="is_free" value="1"
                       {{ old('is_free', ($event?->is_free ?? false)) ? 'checked' : '' }}
                       class="w-5 h-5 rounded-lg border-2 border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-4 focus:ring-indigo-500/20">
                Évènement gratuit (aucun frais d'entrée — le prix sera mis à 0)
            </label>
        </div>

        <script>
            (function () {
                const chk = document.getElementById('is_free');
                const price = document.getElementById('price');
                if (!chk || !price) return;
                function sync() {
                    if (chk.checked) {
                        price.value = 0;
                        price.disabled = true;
                        price.classList.add('opacity-60', 'cursor-not-allowed');
                    } else {
                        price.disabled = false;
                        price.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                }
                chk.addEventListener('change', sync);
                sync();
            })();
        </script>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
        <h2 class="text-lg font-black mb-6 flex items-center gap-3">
            <i class="fa-solid fa-address-book text-indigo-600 dark:text-indigo-400"></i>
            Contact organisateur
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <label for="contact_name" class="{{ $labelClass }}">Nom</label>
                <input type="text" id="contact_name" name="contact_name" maxlength="120"
                       value="{{ old('contact_name', $event->contact_name ?? auth()->user()->name) }}"
                       class="{{ $inputClass }}">
                @error('contact_name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="contact_email" class="{{ $labelClass }}">Email</label>
                <input type="email" id="contact_email" name="contact_email" maxlength="190"
                       value="{{ old('contact_email', $event->contact_email ?? auth()->user()->email) }}"
                       class="{{ $inputClass }}">
                @error('contact_email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="contact_phone" class="{{ $labelClass }}">Téléphone</label>
                <input type="text" id="contact_phone" name="contact_phone" maxlength="30"
                       value="{{ old('contact_phone', $event->contact_phone ?? auth()->user()->phone) }}"
                       placeholder="+228 ..."
                       class="{{ $inputClass }}">
                @error('contact_phone') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
        <h2 class="text-lg font-black mb-6 flex items-center gap-3">
            <i class="fa-solid fa-image text-indigo-600 dark:text-indigo-400"></i>
            Visuel de l'évènement
        </h2>

        <div class="grid md:grid-cols-3 gap-6 items-start">
            <div class="md:col-span-2">
                <label for="image" class="{{ $labelClass }}">Affiche / image (jpg, png, webp — 4 Mo max)</label>
                <input type="file" id="image" name="image" accept="image/*"
                       class="{{ $inputClass }} file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('image') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Sans image, une illustration par défaut selon le type sera utilisée.</p>
            </div>

            @if($event?->image_url)
                <div>
                    <span class="{{ $labelClass }}">Image actuelle</span>
                    <img src="{{ $event->image_url }}" alt="Aperçu" class="w-full h-28 object-cover rounded-2xl border border-slate-100 dark:border-slate-800">
                </div>
            @endif

            @if(auth()->user()->isAdmin())
                <label class="md:col-span-3 flex items-center gap-3 text-sm font-bold text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="is_featured" value="1"
                           {{ old('is_featured', $event->is_featured ?? false) ? 'checked' : '' }}
                           class="w-5 h-5 rounded-lg border-2 border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-4 focus:ring-indigo-500/20">
                    Mettre en avant sur la page d'accueil
                </label>
            @endif
        </div>
    </div>
</div>
