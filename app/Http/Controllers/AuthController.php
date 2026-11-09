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
        // Si l'utilisateur est déjà connecté, on le redirige vers le tableau de bord
        if (Auth::check()) {
            return redirect('/');
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

        // 2. Tentative de connexion (Auth::attempt gère la vérification et le hash du mot de passe)
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Régénère la session pour éviter les attaques par fixation de session
            $request->session()->regenerate();

            return redirect()->intended('/')
                ->with('success', 'Bienvenue sur Hotel Manager, ' . Auth::user()->name . ' !');
        }

        // 3. Échec de connexion : on retourne au formulaire avec une erreur
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
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
