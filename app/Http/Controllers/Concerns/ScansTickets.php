<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Ticket;
use App\Models\User;

trait ScansTickets
{
    /**
     * Validate a scanned ticket code against an event and return a status/message pair.
     *
     * @return array{0: string, 1: string}
     */
    protected function handleScan(?Ticket $ticket, ?User $by = null): array
    {
        if ($ticket === null) {
            return ['invalid', 'Billet introuvable pour cet évènement.'];
        }

        if ($ticket->status === 'used') {
            return ['used', 'Billet déjà scanné le '.$ticket->checked_in_at?->format('d/m/Y à H:i').'.'];
        }

        if ($ticket->status !== 'valid') {
            return ['invalid', 'Ce billet n\'est plus valide (statut : '.$ticket->status.').'];
        }

        $ticket->markUsed($by);

        return ['valid', 'Entrée validée : '.$ticket->holder().' ('.$ticket->ticketType?->name.').'];
    }
}
