<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
            'type' => $request->string('type')->toString(),
            'country' => $request->string('country')->toString(),
            'city' => $request->string('city')->toString(),
            'when' => $request->string('when')->toString(),
            'sort' => $request->string('sort')->toString(),
        ];

        $query = Event::query()->published()->with('user');

        $query->search($filters['q'] ?: null)
            ->ofType($filters['type'] ?: null)
            ->ofCountry($filters['country'] ?: null)
            ->ofCity($filters['city'] ?: null);

        if ($filters['when'] === 'past') {
            $query->past();
        } elseif ($filters['when'] === 'free') {
            $query->where('price', '<=', 0);
        } else {
            $query->upcoming();
        }

        match ($filters['sort']) {
            'recent' => $query->latest('created_at'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->orderBy('starts_at'),
        };

        $events = $query->paginate(9)->withQueryString();

        return view('events.index', [
            'events' => $events,
            'filters' => $filters,
            'types' => collect(config('events.types')),
            'countries' => collect(config('events.countries')),
            'cities' => Event::query()->published()->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function create(): View
    {
        return view('events.create', [
            'types' => collect(config('events.types')),
            'cities' => config('events.cities'),
            'countries' => config('events.countries'),
            'currencies' => config('events.currencies'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($request->boolean('is_free')) {
            $data['price'] = 0;
        }

        $event = new Event($data);
        $event->user_id = $request->user()->id;
        $event->slug = Event::generateSlug($data['title']);
        $event->status = $data['status'] ?? 'published';
        $event->published_at = now();

        if ($image) {
            $event->image = $image->store('events', 'public');
        }

        $event->save();

        $event->ticketTypes()->create([
            'name' => 'Standard',
            'description' => 'Billet standard pour l\'évènement.',
            'price' => $data['price'] ?? 0,
            'currency' => $data['currency'] ?? 'FCFA',
            'quantity' => $data['capacity'] ?? 100,
            'sold' => 0,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $request->user()->promoteToOrganizer();

        return redirect()
            ->route('ticket-types.index', $event)
            ->with('success', 'Votre évènement a été créé avec succès. Ajoutez vos catégories de billets (VIP, Standard, etc.) et leurs quantités.');
    }

    public function join(Request $request, Event $event): RedirectResponse
    {
        abort_unless($event->status === 'published', 404);

        if ($event->isPast()) {
            return redirect()->route('events.show', $event)->with('error', 'Cet évènement est terminé.');
        }

        if (! $event->is_free) {
            return redirect()->route('orders.create', $event);
        }

        $existing = Ticket::query()
            ->where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return redirect()->route('tickets.show', $existing)->with('info', 'Vous participez déjà à cet évènement.');
        }

        $type = $event->ticketTypes()->where('price', '<=', 0)->first();

        if (! $type) {
            $type = TicketType::create([
                'event_id' => $event->id,
                'name' => 'Entrée gratuite',
                'description' => 'Accès gratuit à l\'évènement.',
                'price' => 0,
                'currency' => $event->currency ?: 'FCFA',
                'quantity' => 100000,
                'sold' => 0,
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }

        $order = Order::create([
            'reference' => Order::generateReference(),
            'event_id' => $event->id,
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'total_amount' => 0,
            'currency' => $event->currency ?: 'FCFA',
        ]);

        $item = $order->items()->create([
            'ticket_type_id' => $type->id,
            'quantity' => 1,
            'unit_price' => 0,
            'subtotal' => 0,
        ]);

        $ticket = Ticket::create([
            'code' => Ticket::generateCode(),
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'ticket_type_id' => $type->id,
            'event_id' => $event->id,
            'user_id' => $request->user()->id,
            'holder_name' => $request->user()->name,
            'status' => 'valid',
        ]);

        $type->increment('sold', 1);
        $order->update(['total_amount' => 0]);
        $order->markPaid();

        return redirect()->route('tickets.show', $ticket)->with('success', 'Vous participez maintenant à cet évènement.');
    }

    public function show(Event $event): View
    {
        if ($event->status !== 'published' && !$event->canBeManagedBy(auth()->user())) {
            abort(404);
        }

        if ($event->ticketTypes()->count() === 0) {
            $event->ticketTypes()->create([
                'name' => 'Standard',
                'description' => 'Billet standard pour l\'évènement.',
                'price' => $event->price,
                'currency' => $event->currency ?: 'FCFA',
                'quantity' => $event->capacity ?: 100,
                'sold' => 0,
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }

        $related = Event::query()
            ->published()
            ->where('id', '!=', $event->id)
            ->where(function ($q) use ($event) {
                $q->where('type', $event->type)->orWhere('city', $event->city);
            })
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        return view('events.show', [
            'event' => $event,
            'related' => $related,
            'topCandidates' => $event->isCompetition()
                ? $event->candidates()
                    ->active()
                    ->withScore()
                    ->orderByRaw('(SELECT COALESCE(SUM(value), 0) FROM competition_votes WHERE competition_votes.candidate_id = competition_candidates.id) DESC')
                    ->orderBy('name')
                    ->limit(3)
                    ->get()
                : collect(),
            'candidatesCount' => $event->isCompetition() ? $event->candidates()->active()->count() : 0,
            'ticketTypes' => $event->ticketTypes()->active()->ordered()->get(),
        ]);
    }

    public function edit(Event $event): View
    {
        $this->authorizeEvent($event);

        return view('events.edit', [
            'event' => $event,
            'types' => collect(config('events.types')),
            'cities' => config('events.cities'),
            'countries' => config('events.countries'),
            'currencies' => config('events.currencies'),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        $data = $this->validateData($request, $event);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($request->boolean('is_free')) {
            $data['price'] = 0;
        }

        $event->fill($data);
        $event->slug = $event->title === $event->getOriginal('title')
            ? $event->slug
            : Event::generateSlug($data['title'], $event->id);
        $event->status = $data['status'] ?? $event->status;

        if ($request->user()->isAdmin()) {
            $event->is_featured = $request->boolean('is_featured');
        }

        if ($image) {
            if ($event->image && !str_starts_with($event->image, 'http')) {
                Storage::disk('public')->delete($event->image);
            }
            $event->image = $image->store('events', 'public');
        }

        $event->save();

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Votre évènement a été mis à jour.');
    }

    public function destroy(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeEvent($event);

        if ($event->image && !str_starts_with($event->image, 'http')) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()
            ->back()
            ->with('success', 'L\'évènement a été supprimé.');
    }

    public function mine(Request $request): View
    {
        $user = $request->user();

        $scope = fn () => Event::query()->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));

        $events = $scope()
            ->orderByDesc('starts_at')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'events' => $scope()->count(),
            'published' => $scope()->where('status', 'published')->count(),
            'tickets' => Ticket::query()->whereIn('event_id', $scope()->select('id'))->count(),
            'checked_in' => Ticket::query()->whereIn('event_id', $scope()->select('id'))->where('status', 'used')->count(),
            'revenue' => (float) Order::query()->whereIn('event_id', $scope()->select('id'))->paid()->sum('total_amount'),
        ];

        return view('events.mine', [
            'events' => $events,
            'stats' => $stats,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function validateData(Request $request, ?Event $event = null): array
    {
        $maxDate = now()->addYears(5)->format('Y-m-d');

        return $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'type' => ['required', Rule::in(array_keys(config('events.types')))],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'starts_at' => ['required', 'date', 'after_or_equal:now', 'before_or_equal:'.$maxDate],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at', 'before_or_equal:'.$maxDate],
            'venue' => ['required', 'string', 'min:2', 'max:150'],
            'address' => ['nullable', 'string', 'max:180'],
            'city' => ['required', Rule::in(collect(config('events.cities_by_country'))->flatten()->unique()->values()->all())],
            'country' => ['required', Rule::in(config('events.countries'))],
            'price' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'currency' => ['nullable', Rule::in(array_keys(config('events.currencies')))],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', Rule::in(['published', 'draft', 'cancelled'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function authorizeEvent(Event $event): void
    {
        abort_unless($event->canBeManagedBy(auth()->user()), 403, 'Vous n\'êtes pas autorisé à gérer cet évènement.');
    }
}
