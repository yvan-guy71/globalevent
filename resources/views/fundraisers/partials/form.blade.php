@php
    $fundraiser = $fundraiser ?? null;
    $inputClass = 'w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors';
    $labelClass = 'block text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="{{ $labelClass }}">Titre de la cagnotte *</label>
        <input type="text" name="title" required value="{{ old('title', $fundraiser?->title) }}"
               placeholder="Ex : Soutien au Festival des Arts de Lomé" class="{{ $inputClass }}">
    </div>

    <div>
        <label class="{{ $labelClass }}">Description *</label>
        <textarea name="description" rows="6" required placeholder="Expliquez la cause, ce que vous financez et pourquoi c'est important..."
                  class="{{ $inputClass }} resize-y">{{ old('description', $fundraiser?->description) }}</textarea>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <label class="{{ $labelClass }}">Objectif à atteindre *</label>
            <input type="number" name="goal_amount" min="1000" step="500" required
                   value="{{ old('goal_amount', $fundraiser?->goal_amount) }}" placeholder="500000" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Devise</label>
            <select name="currency" class="{{ $inputClass }}">
                @foreach($currencies as $code => $label)
                    <option value="{{ $code }}" {{ old('currency', $fundraiser?->currency ?? 'FCFA') === $code ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelClass }}">Date limite (optionnel)</label>
            <input type="date" name="deadline"
                   value="{{ old('deadline', $fundraiser?->deadline?->format('Y-m-d')) }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Statut</label>
            <select name="status" class="{{ $inputClass }}">
                <option value="open" {{ old('status', $fundraiser?->status ?? 'open') === 'open' ? 'selected' : '' }}>Ouverte aux dons</option>
                <option value="closed" {{ old('status', $fundraiser?->status) === 'closed' ? 'selected' : '' }}>Clôturée</option>
            </select>
        </div>
    </div>

    <div>
        <label class="{{ $labelClass }}">Visuel (optionnel)</label>
        <input type="file" name="image" accept="image/*" class="{{ $inputClass }} file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-600 file:text-white">
    </div>

    <fieldset class="grid sm:grid-cols-3 gap-4">
        <legend class="{{ $labelClass }}">Contact (optionnel)</legend>
        <input type="text" name="contact_name" value="{{ old('contact_name', $fundraiser?->contact_name) }}" placeholder="Nom" class="{{ $inputClass }}">
        <input type="email" name="contact_email" value="{{ old('contact_email', $fundraiser?->contact_email) }}" placeholder="Email" class="{{ $inputClass }}">
        <input type="text" name="contact_phone" value="{{ old('contact_phone', $fundraiser?->contact_phone) }}" placeholder="Téléphone" class="{{ $inputClass }}">
    </fieldset>

    <button type="submit" class="w-full px-6 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-colors">
        <i class="fa-solid fa-hand-holding-heart mr-2"></i>{{ $submitLabel }}
    </button>
</form>
