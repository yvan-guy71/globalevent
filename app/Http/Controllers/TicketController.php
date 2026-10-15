<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = $request->user()->tickets()
            ->with(['event', 'ticketType', 'order'])
            ->latest()
            ->paginate(12);

        return view('tickets.index', [
            'tickets' => $tickets,
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
