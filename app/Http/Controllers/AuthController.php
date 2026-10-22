<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Identifiants incorrects. Vérifiez votre email et votre mot de passe.']);
        }

        if (Auth::user()?->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Les administrateurs doivent se connecter via l\'espace administrateur dédié.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Bonjour '.Auth::user()->name.' !');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'email.unique' => 'Cette adresse e-mail est déjà utilisée. Veuillez vous connecter ou utiliser une autre adresse.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'organization' => $data['organization'] ?? null,
            'password' => $data['password'],
            'role' => 'client',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Bienvenue '.$user->name.' ! Votre compte est créé.')
            ->with('confetti', true);
    }

    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Identifiants incorrects. Vérifiez votre email et votre mot de passe.']);
        }

        if (! Auth::user()?->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Cet espace est réservé aux administrateurs.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Bonjour '.Auth::user()->name.' !');
    }

    public function showAdminRegister(): View
    {
        return view('auth.admin-register');
    }

    public function adminRegister(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'email.unique' => 'Cette adresse e-mail est déjà utilisée. Veuillez vous connecter ou utiliser une autre adresse.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'organization' => $data['organization'] ?? null,
            'password' => $data['password'],
            'role' => 'admin',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Bienvenue '.$user->name.' ! Votre compte administrateur est créé.')
            ->with('confetti', true);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Vous êtes déconnecté(e). À bientôt !');
    }

    public function becomeOrganizer(Request $request): RedirectResponse
    {
        if ($request->user()->promoteToOrganizer()) {
            return back()->with('success', 'Félicitations ! Vous êtes désormais organisateur. Créez et gérez vos évènements.');
        }

        return back()->with('info', 'Vous disposez déjà d\'un accès organisateur.');
    }
}
