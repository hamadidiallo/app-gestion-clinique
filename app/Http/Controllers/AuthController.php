<?php

namespace App\Http\Controllers;

// Importations des classes nécessaires pour l'authentification
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Contrôleur gérant l'authentification des utilisateurs (Connexion, Inscription et Déconnexion).
 */
class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion (Login).
     *
     * @return View
     */
    public function showLoginForm()
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Retourne la vue de connexion
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion de l'utilisateur.
     *
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        // Validation des champs du formulaire de connexion
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Tentative d'authentification de l'utilisateur
        if (Auth::attempt($credentials, $request->has('remember'))) {
            // Régénération de la session pour des raisons de sécurité
            $request->session()->regenerate();

            // Redirection vers le tableau de bord avec message de bienvenue
            return redirect()->intended(route('dashboard'))->with('alert', 'Connexion réussie ! Bienvenue dans CLINGEST.');
        }

        // En cas d'échec d'authentification
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent à aucun compte enregistré.',
        ])->onlyInput('email');
    }

    /**
     * Affiche le formulaire d'inscription (Register).
     *
     * @return View
     */
    public function showRegisterForm()
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Récupération des rôles disponibles pour l'attribution lors de l'inscription
        $roles = Role::pluck('nom', 'id');

        // Retourne la vue d'inscription
        return view('auth.register', compact('roles'));
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur dans le système.
     *
     * @return RedirectResponse
     */
    public function register(Request $request)
    {
        // Validation des données d'inscription
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'nom.required' => 'Le nom de famille est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role_id.required' => 'Le choix d\'un rôle est obligatoire.',
        ]);

        // Création du compte utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        // Connecter immédiatement le nouvel utilisateur
        Auth::login($user);

        // Redirection vers le tableau de bord
        return redirect()->route('dashboard')->with('alert', 'Inscription réussie ! Votre compte a été créé.');
    }

    /**
     * Traite la déconnexion de l'utilisateur connecté (Logout).
     *
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        // Déconnexion de la session actuelle
        Auth::logout();

        // Invalidation de la session et régénération du jeton CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirection vers la page de connexion
        return redirect()->route('login')->with('alert', 'Vous avez été déconnecté avec succès.');
    }
}
