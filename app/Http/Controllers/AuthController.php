<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion.
     */
    public function showLoginForm()
    {
        // Si l'utilisateur est déjà connecté, on le redirige selon son rôle
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Traiter la demande de connexion.
     */
    public function login(Request $request)
    {
        // 1. Validation des saisies
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // 2. Tentative de connexion
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            return $this->redirectByRole($user)
                ->with('success', 'Bienvenue sur Hotel Manager, ' . $user->name . ' !');
        }

        // 3. Échec de connexion
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Déterminer la page d'accueil selon le rôle de l'employé.
     */
    protected function redirectByRole($user)
    {
        if ($user->hasRole('gouvernante')) {
            return redirect()->route('housekeeping.index');
        }

        if ($user->hasRole('restauration')) {
            return redirect()->route('orders.create');
        }

        if ($user->hasRole('receptionniste')) {
            return redirect()->route('reservations.rack');
        }

        // Par défaut (admin / direction) : Tableau de bord KPIs
        return redirect()->route('dashboard');
    }

    /**
     * Déconnecter l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
