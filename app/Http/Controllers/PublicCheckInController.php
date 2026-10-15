<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScansTickets;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCheckInController extends Controller
{
    use ScansTickets;

    public function show(Request $request, Event $event, string $token): View
    {
        $this->authorizeToken($event, $token);

        $total = $event->tickets()->count();
        $used = $event->tickets()->used()->count();

        $recent = $event->tickets()
            ->used()
            ->with('ticketType')
            ->latest('checked_in_at')
            ->limit(10)
            ->get();

        return view('scan.show', [
            'event' => $event,
            'token' => $token,
            'stats' => [
                'total' => $total,
                'used' => $used,
                'remaining' => max(0, $total - $used),
            ],
            'recent' => $recent,
        ]);
    }

    public function process(Request $request, Event $event, string $token): JsonResponse|RedirectResponse
    {
        $this->authorizeToken($event, $token);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:40'],
        ]);

        $code = strtoupper(trim($data['code']));

        $ticket = $event->tickets()
            ->with('ticketType')
            ->where('code', $code)
            ->first();

        [$status, $message] = $this->handleScan($ticket, null);

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

    private function authorizeToken(Event $event, string $token): void
    {
        abort_unless(
            $event->hasScanShare() && hash_equals((string) $event->scan_token, $token),
            404
        );
    }
}
