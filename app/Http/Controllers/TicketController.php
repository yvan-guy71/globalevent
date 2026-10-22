<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $selectedEvent = $request->integer('event') ?: null;

        $events = $request->user()->tickets()
            ->with('event')
            ->get()
            ->pluck('event')
            ->filter()
            ->unique('id')
            ->values();

        $tickets = $request->user()->tickets()
            ->with(['event', 'ticketType', 'order'])
            ->when($selectedEvent, fn ($query) => $query->where('event_id', $selectedEvent))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'events' => $events,
            'selectedEvent' => $selectedEvent,
        ]);
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless(
            $ticket->user_id === $request->user()->id || $request->user()->isAdmin(),
            403
        );

        $ticket->load(['event', 'ticketType', 'order']);

        return view('tickets.show', [
            'ticket' => $ticket,
        ]);
    }
}
