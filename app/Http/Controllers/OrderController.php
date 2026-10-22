<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Event $event): View
    {
        $this->ensureOrderable($event);

        $ticketTypes = $event->ticketTypes()
            ->active()
            ->ordered()
            ->get();

        abort_if($ticketTypes->isEmpty(), 404);

        return view('orders.create', [
            'event' => $event,
            'ticketTypes' => $ticketTypes,
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->ensureOrderable($event);

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $selections = collect($data['items'])
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($selections->isEmpty()) {
            return back()->withErrors(['items' => 'Sélectionnez au moins un billet.'])->withInput();
        }

        $ticketTypes = $event->ticketTypes()
            ->whereIn('id', $selections->keys())
            ->get()
            ->keyBy('id');

        foreach ($selections as $typeId => $quantity) {
            $type = $ticketTypes->get((int) $typeId);
            abort_if($type === null, 404);
            abort_unless($type->canPurchase($quantity), 422, 'La catégorie « '.$type->name.' » n\'est plus disponible en quantité suffisante.');
        }

        $order = DB::transaction(function () use ($event, $request, $ticketTypes, $selections) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'event_id' => $event->id,
                'reference' => Order::generateReference(),
                'total_amount' => 0,
                'currency' => $event->currency ?: 'FCFA',
                'status' => 'pending',
            ]);

            $total = 0.0;

            foreach ($selections as $typeId => $quantity) {
                $type = $ticketTypes->get((int) $typeId);
                $unitPrice = (float) $type->price;
                $subtotal = $unitPrice * $quantity;
                $total += $subtotal;

                $item = $order->items()->create([
                    'ticket_type_id' => $type->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                for ($i = 0; $i < $quantity; $i++) {
                    Ticket::create([
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'ticket_type_id' => $type->id,
                        'event_id' => $event->id,
                        'user_id' => $request->user()->id,
                        'code' => Ticket::generateCode(),
                        'holder_name' => $request->user()->name,
                        'status' => 'valid',
                    ]);
                }

                $type->increment('sold', $quantity);
            }

            $order->update([
                'total_amount' => $total,
                'currency' => $ticketTypes->first()->currency ?? $order->currency,
            ]);

            $order->markPaid();

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Paiement simulé accepté. Vos billets sont disponibles !');
    }

    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with(['event', 'tickets'])
            ->withCount('tickets')
            ->latest()
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless(
            $order->user_id === $request->user()->id || $request->user()->isAdmin() || $order->event->canBeManagedBy($request->user()),
            403
        );

        $order->load(['event', 'items.ticketType', 'tickets.ticketType']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function ensureOrderable(Event $event): void
    {
        abort_unless($event->status === 'published' || $event->canBeManagedBy(auth()->user()), 404);
        abort_if($event->isPast(), 410, 'Cet évènement est terminé.');
    }
}
