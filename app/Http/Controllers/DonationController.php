<?php

namespace App\Http\Controllers;

use App\Models\Fundraiser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    public function store(Request $request, Fundraiser $fundraiser): RedirectResponse
    {
        abort_unless($fundraiser->isOpen(), 403, 'Cette cagnotte est clôturée : les dons ne sont plus acceptés.');

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:500', 'max:100000000'],
            'message' => ['nullable', 'string', 'max:500'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        $donation = $fundraiser->donations()->create([
            'user_id' => $request->user()->id,
            'donor_name' => $request->user()->name,
            'amount' => $data['amount'],
            'currency' => $fundraiser->currency,
            'message' => $data['message'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'paid',
            'reference' => 'DON-'.Str::upper(Str::random(10)),
        ]);

        return redirect()
            ->route('fundraisers.show', $fundraiser)
            ->with('success', 'Merci ! Votre don de '.$donation->amount_label.' a été enregistré (paiement simulé).');
    }
}
