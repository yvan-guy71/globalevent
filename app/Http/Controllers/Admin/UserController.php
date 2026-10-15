<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = ['client', 'organisateur', 'admin'];

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $filters = [
            'q' => $request->string('q')->toString(),
            'role' => $request->string('role')->toString(),
        ];

        $users = User::query()
            ->withCount('events', 'orders')
            ->when($filters['q'], function ($q, $term) {
                $like = '%'.$term.'%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('organization', 'like', $like));
            })
            ->when($filters['role'], fn ($q, $role) => $q->where('role', $role))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'filters' => $filters,
            'roles' => self::ROLES,
            'stats' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'organizers' => User::where('role', 'organisateur')->count(),
                'clients' => User::where('role', 'client')->count(),
            ],
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'role' => ['required', Rule::in(self::ROLES)],
        ]);

        if ($user->is($request->user())) {
            return back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        if ($user->isAdmin() && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Impossible de retirer le dernier administrateur.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with('success', 'Le rôle de '.$user->name.' a été mis à jour.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($user->is($request->user())) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', 'Le compte de '.$name.' a été supprimé.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
