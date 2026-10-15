<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Fundraiser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FundraiserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();

        $fundraisers = Fundraiser::query()
            ->open()
            ->withProgress()
            ->search($search ?: null)
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('fundraisers.index', [
            'fundraisers' => $fundraisers,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('fundraisers.create', [
            'currencies' => config('events.currencies'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        $fundraiser = new Fundraiser($data);
        $fundraiser->user_id = $request->user()->id;
        $fundraiser->slug = Fundraiser::generateSlug($data['title']);
        $fundraiser->status = $data['status'] ?? 'open';

        if ($image) {
            $fundraiser->image = $image->store('fundraisers', 'public');
        }

        $fundraiser->save();

        return redirect()
            ->route('fundraisers.show', $fundraiser)
            ->with('success', 'Votre cagnotte a été créée. Partagez-la pour récolter vos premiers dons !');
    }

    public function show(Request $request, Fundraiser $fundraiser): View
    {
        $donations = $fundraiser->donations()
            ->paid()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('fundraisers.show', [
            'fundraiser' => $fundraiser,
            'donations' => $donations,
            'canManage' => $fundraiser->canBeManagedBy($request->user()),
            'canDonate' => $fundraiser->isOpen(),
        ]);
    }

    public function edit(Fundraiser $fundraiser): View
    {
        $this->authorizeManagement($fundraiser);

        return view('fundraisers.edit', [
            'fundraiser' => $fundraiser,
            'currencies' => config('events.currencies'),
        ]);
    }

    public function update(Request $request, Fundraiser $fundraiser): RedirectResponse
    {
        $this->authorizeManagement($fundraiser);

        $data = $this->validateData($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        $fundraiser->fill($data);
        $fundraiser->slug = $fundraiser->title === $fundraiser->getOriginal('title')
            ? $fundraiser->slug
            : Fundraiser::generateSlug($data['title'], $fundraiser->id);
        $fundraiser->status = $data['status'] ?? $fundraiser->status;

        if ($image) {
            if ($fundraiser->image && ! str_starts_with($fundraiser->image, 'http')) {
                Storage::disk('public')->delete($fundraiser->image);
            }
            $fundraiser->image = $image->store('fundraisers', 'public');
        }

        $fundraiser->save();

        return redirect()
            ->route('fundraisers.show', $fundraiser)
            ->with('success', 'Votre cagnotte a été mise à jour.');
    }

    public function destroy(Fundraiser $fundraiser): RedirectResponse
    {
        $this->authorizeManagement($fundraiser);

        if ($fundraiser->image && ! str_starts_with($fundraiser->image, 'http')) {
            Storage::disk('public')->delete($fundraiser->image);
        }

        $fundraiser->delete();

        return redirect()
            ->route('fundraisers.index')
            ->with('success', 'La cagnotte a été supprimée.');
    }

    public function mine(Request $request): View
    {
        $user = $request->user();

        $fundraisers = Fundraiser::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->withProgress()
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('fundraisers.mine', [
            'fundraisers' => $fundraisers,
            'totalRaised' => $fundraisers->getCollection()->sum('raised_amount'),
            'totalDonations' => Donation::query()
                ->whereIn('fundraiser_id', $fundraisers->pluck('id'))
                ->paid()
                ->count(),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'min:4', 'max:140'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'goal_amount' => ['required', 'numeric', 'min:1000', 'max:1000000000'],
            'currency' => ['nullable', 'in:'.implode(',', array_keys(config('events.currencies')))],
            'deadline' => ['nullable', 'date', 'after:today'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'in:open,closed'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function authorizeManagement(Fundraiser $fundraiser): void
    {
        abort_unless($fundraiser->canBeManagedBy(auth()->user()), 403, 'Vous ne pouvez pas gérer cette cagnotte.');
    }
}
