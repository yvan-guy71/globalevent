<?php

namespace App\Http\Controllers;

use App\Models\CompetitionCandidate;
use App\Models\CompetitionVote;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionVoteController extends Controller
{
    public function store(Request $request, Event $event, CompetitionCandidate $candidate): RedirectResponse
    {
        $this->ensureVotable($request, $event, $candidate);

        $value = (int) $request->validate([
            'value' => ['required', Rule::in([CompetitionVote::FOR, CompetitionVote::AGAINST])],
        ])['value'];

        $existing = CompetitionVote::query()
            ->where('candidate_id', $candidate->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing && $existing->value === $value) {
            $existing->delete();

            return back()->with('success', 'Votre vote pour « '.$candidate->name.' » a été annulé.');
        }

        CompetitionVote::updateOrCreate(
            [
                'candidate_id' => $candidate->id,
                'user_id' => $request->user()->id,
            ],
            [
                'event_id' => $event->id,
                'value' => $value,
            ],
        );

        return back()->with('success', $value === CompetitionVote::FOR
            ? 'Merci ! Votre vote pour « '.$candidate->name.' » a été enregistré.'
            : 'Votre vote contre « '.$candidate->name.' » a été enregistré.');
    }

    public function destroy(Request $request, Event $event, CompetitionCandidate $candidate): RedirectResponse
    {
        $this->ensureVotable($request, $event, $candidate);

        CompetitionVote::query()
            ->where('candidate_id', $candidate->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return back()->with('success', 'Votre vote pour « '.$candidate->name.' » a été retiré.');
    }

    private function ensureVotable(Request $request, Event $event, CompetitionCandidate $candidate): void
    {
        abort_unless($event->isCompetition(), 404);
        abort_unless($candidate->event_id === $event->id, 404);
        abort_unless($event->isPast() === false, 403, 'Cette compétition est terminée, les votes sont clos.');
        abort_unless(! $event->isOwnedBy($request->user()), 403, 'Vous ne pouvez pas voter dans votre propre compétition.');
    }
}
