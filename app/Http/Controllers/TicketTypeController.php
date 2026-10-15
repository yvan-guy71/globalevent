<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorizeManagement($event);

        $ticketTypes = $event->ticketTypes()
            ->ordered()
            ->withCount('tickets')
            ->get();

        return view('tickets.manage', [
            'event' => $event,
            'ticketTypes' => $ticketTypes,
            'currencies' => config('events.currencies'),
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeManagement($event);

        $data = $this->validateType($request);

        $ticketType = new TicketType($data);
        $ticketType->event_id = $event->id;
        $ticketType->currency = $data['currency'] ?? $event->currency ?? 'FCFA';
        $ticketType->is_active = $request->boolean('is_active', true);
        $ticketType->save();

        return redirect()
            ->route('ticket-types.index', $event)
            ->with('success', 'La catégorie de billet a été ajoutée.');
    }

    public function update(Request $request, Event $event, TicketType $ticketType): RedirectResponse
    {
        $this->authorizeManagement($event);
        $this->ensureBelongsToEvent($event, $ticketType);

        $data = $this->validateType($request);

        $ticketType->fill($data);
        $ticketType->currency = $data['currency'] ?? $ticketType->currency;
        $ticketType->is_active = $request->boolean('is_active');
        $ticketType->save();

        return redirect()
            ->route('ticket-types.index', $event)
            ->with('success', 'La catégorie de billet a été mise à jour.');
    }

    public function destroy(Event $event, TicketType $ticketType): RedirectResponse
    {
        $this->authorizeManagement($event);
        $this->ensureBelongsToEvent($event, $ticketType);

        abort_if($ticketType->tickets()->exists(), 403, 'Impossible de supprimer une catégorie déjà vendue. Désactivez-la plutôt.');

        $ticketType->delete();

        return redirect()
            ->route('ticket-types.index', $event)
            ->with('success', 'La catégorie de billet a été supprimée.');
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function validateType(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'currency' => ['nullable', 'in:'.implode(',', array_keys(config('events.currencies')))],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function ensureBelongsToEvent(Event $event, TicketType $ticketType): void
    {
        abort_unless($ticketType->event_id === $event->id, 404);
    }

    private function authorizeManagement(Event $event): void
    {
        abort_unless($event->canBeManagedBy(auth()->user()), 403, 'Vous ne pouvez pas gérer la billetterie de cet évènement.');
    }
}
