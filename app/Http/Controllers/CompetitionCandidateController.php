<?php

namespace App\Http\Controllers;

use App\Models\CompetitionCandidate;
use App\Models\CompetitionVote;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompetitionCandidateController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->ensureCompetition($event);

        $candidates = $event->candidates()
            ->withScore()
            ->orderByRaw('(SELECT COALESCE(SUM(value), 0) FROM competition_votes WHERE competition_votes.candidate_id = competition_candidates.id) DESC')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $userVotes = $request->user()
            ? CompetitionVote::query()
                ->where('event_id', $event->id)
                ->where('user_id', $request->user()->id)
                ->get()
                ->keyBy('candidate_id')
            : collect();

        return view('competitions.index', [
            'event' => $event,
            'candidates' => $candidates,
            'userVotes' => $userVotes,
            'canManage' => $event->canBeManagedBy($request->user()),
            'canVote' => $request->user() !== null && ! $event->isOwnedBy($request->user()),
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->ensureCompetition($event);
        $this->authorizeManagement($event);

        $data = $this->validateCandidate($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        $candidate = new CompetitionCandidate($data);
        $candidate->event_id = $event->id;
        $candidate->user_id = $request->user()->id;
        $candidate->slug = CompetitionCandidate::generateSlug($data['name'], $event->id);
        $candidate->is_active = $request->boolean('is_active', true);

        if ($image) {
            $candidate->image = $image->store('candidates', 'public');
        }

        $candidate->save();

        return redirect()
            ->route('competitions.index', $event)
            ->with('success', 'Le candidat a été ajouté à la compétition.');
    }

    public function update(Request $request, Event $event, CompetitionCandidate $candidate): RedirectResponse
    {
        $this->ensureCompetition($event);
        $this->ensureBelongsToEvent($event, $candidate);
        $this->authorizeManagement($event);

        $data = $this->validateCandidate($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        $candidate->fill($data);
        $candidate->slug = CompetitionCandidate::generateSlug($data['name'], $event->id, $candidate->id);
        $candidate->is_active = $request->boolean('is_active');

        if ($image) {
            if ($candidate->image && ! str_starts_with($candidate->image, 'http')) {
                Storage::disk('public')->delete($candidate->image);
            }
            $candidate->image = $image->store('candidates', 'public');
        }

        $candidate->save();

        return redirect()
            ->route('competitions.index', $event)
            ->with('success', 'Le candidat a été mis à jour.');
    }

    public function destroy(Event $event, CompetitionCandidate $candidate): RedirectResponse
    {
        $this->ensureCompetition($event);
        $this->ensureBelongsToEvent($event, $candidate);
        $this->authorizeManagement($event);

        if ($candidate->image && ! str_starts_with($candidate->image, 'http')) {
            Storage::disk('public')->delete($candidate->image);
        }

        $candidate->delete();

        return redirect()
            ->route('competitions.index', $event)
            ->with('success', 'Le candidat a été retiré de la compétition.');
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function validateCandidate(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function ensureCompetition(Event $event): void
    {
        abort_unless($event->isCompetition(), 404);
    }

    private function ensureBelongsToEvent(Event $event, CompetitionCandidate $candidate): void
    {
        abort_unless($candidate->event_id === $event->id, 404);
    }

    private function authorizeManagement(Event $event): void
    {
        abort_unless($event->canBeManagedBy(auth()->user()), 403, 'Vous ne pouvez pas gérer les candidats de cette compétition.');
    }
}
