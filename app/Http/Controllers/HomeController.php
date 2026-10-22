<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $types = collect(config('events.types'));

        $typeCounts = Event::query()
            ->published()
            ->upcoming()
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $featured = Event::query()
            ->published()
            ->featured()
            ->with('user')
            ->orderByDesc('starts_at')
            ->limit(6)
            ->get();

        $upcoming = Event::query()
            ->published()
            ->upcoming()
            ->with('user')
            ->orderBy('starts_at')
            ->limit(9)
            ->get();

        $stats = [
            'events' => Event::query()->published()->count(),
            'cities' => Event::query()->published()->distinct()->count('city'),
            'organizers' => Event::query()->published()->distinct()->count('user_id'),
            'free' => Event::query()->published()->where('price', '<=', 0)->count(),
        ];

        return view('home', [
            'types' => $types,
            'typeCounts' => $typeCounts,
            'featured' => $featured,
            'upcoming' => $upcoming,
            'stats' => $stats,
            'countries' => config('events.countries'),
        ]);
    }
}
