<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $filters = [
            'q' => $request->string('q')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        $events = Event::query()
            ->with('user')
            ->search($filters['q'] ?: null)
            ->when($filters['status'], fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events', [
            'events' => $events,
            'filters' => $filters,
            'stats' => [
                'events' => Event::count(),
                'published' => Event::where('status', 'published')->count(),
                'drafts' => Event::where('status', 'draft')->count(),
                'users' => User::count(),
            ],
        ]);
    }
}
