<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScansTickets;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInController extends Controller
{
    use ScansTickets;

    public function show(Event $event): View
    {
        $this->authorizeManagement($event);

        $total = $event->tickets()->count();
        $used = $event->tickets()->used()->count();

        $recent = $event->tickets()
            ->used()
            ->with('ticketType')
            ->latest('checked_in_at')
            ->limit(10)
            ->get();

        return view('checkin.show', [
            'event' => $event,
            'stats' => [
                'total' => $total,
                'used' => $used,
                'remaining' => max(0, $total - $used),
            ],
            'recent' => $recent,
        ]);
    }

    public function process(Request $request, Event $event): JsonResponse|RedirectResponse
    {
        $this->authorizeManagement($event);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:40'],
        ]);

        $code = strtoupper(trim($data['code']));

        $ticket = $event->tickets()
            ->with('ticketType')
            ->where('code', $code)
            ->first();

        [$status, $message] = $this->handleScan($ticket, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'ticket' => $ticket ? [
                    'code' => $ticket->code,
                    'holder' => $ticket->holder(),
                    'type' => $ticket->ticketType?->name,
                    'status' => $ticket->status,
                    'checked_in_at' => $ticket->checked_in_at?->format('d/m/Y H:i'),
                ] : null,
            ], $status === 'valid' ? 200 : 422);
        }

        return back()->with($status === 'valid' ? 'success' : 'error', $message);
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public function enableShare(Event $event): RedirectResponse
    {
        $this->authorizeManagement($event);

        if (! $event->hasScanShare()) {
            $event->generateScanToken();
        }

        return back()->with('success', 'Lien de scan partagé activé. Copiez-le et transmettez-le à votre équipe de sécurité.');
    }

    public function disableShare(Event $event): RedirectResponse
    {
        $this->authorizeManagement($event);

        $event->revokeScanToken();

        return back()->with('success', 'Le lien de scan partagé a été révoqué.');
    }

    private function authorizeManagement(Event $event): void
    {
        abort_unless($event->canBeManagedBy(auth()->user()), 403, 'Vous ne pouvez pas contrôler les billets de cet évènement.');
    }
}
