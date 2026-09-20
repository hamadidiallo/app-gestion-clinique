<?php

namespace App\Http\Controllers;

// Importations des classes nécessaires pour l'authentification
use App\Models\Clinique;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function showLoginForm(Request $request)
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Récupération des rôles collaborateurs disponibles pour rejoindre une clinique
        $roles = Role::whereNotIn('nom', ['Super Administrateur', 'Admin'])
            ->orderBy('nom')
            ->pluck('nom', 'id');

        $activeTab = $request->query('tab', 'Connexion');

        // Retourne la vue de connexion
        return view('auth.login', [
            'roles' => $roles,
            'activeTab' => $activeTab,
        ]);
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
            'email' => 'required',
            'password' => 'required|string',
        ], [
            'email.required' => 'L\'identifiant ou l\'adresse email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Si l'identifiant n'est pas un email strict, chercher l'utilisateur par email ou par début d'email
        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'email';

        // Tentative d'authentification de l'utilisateur
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->has('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))->with('alert', 'Connexion réussie ! Bienvenue dans votre espace clinique.');
        }

        // Si la connexion directe échoue et qu'un pseudo était fourni, chercher si un email commence par ce pseudo
        if (! filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            $candidate = User::where('email', 'like', $credentials['email'].'@%')->first();
            if ($candidate && Hash::check($credentials['password'], $candidate->password)) {
                Auth::login($candidate, $request->has('remember'));
                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'))->with('alert', 'Connexion réussie ! Bienvenue dans votre espace clinique.');
            }
        }

        // En cas d'échec d'authentification
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent à aucun compte enregistré.',
        ])->withInput($request->except('password'))->with('error_tab', 'Connexion');
    }

    /**
     * Affiche le formulaire d'inscription (Register) avec choix Rejoindre ou Créer.
     *
     * @return View
     */
    public function showRegisterForm(Request $request)
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Récupération des rôles collaborateurs disponibles pour rejoindre une clinique
        $roles = Role::whereNotIn('nom', ['Super Administrateur', 'Admin'])
            ->orderBy('nom')
            ->pluck('nom', 'id');

        $tabParam = strtolower((string) $request->query('tab', ''));
        if ($tabParam === 'rejoindre' || $tabParam === 'invitation') {
            $activeTab = 'Rejoindre';
        } elseif ($tabParam === 'connexion' || $tabParam === 'login') {
            $activeTab = 'Connexion';
        } elseif ($tabParam === 'clinique' || $tabParam === 'creer') {
            $activeTab = 'Clinique';
        } else {
            $activeTab = 'Bienvenue';
        }

        // Retourne la vue d'inscription
        return view('auth.register', compact('roles', 'activeTab'));
    }

    /**
     * Vérifie dynamiquement la validité d'un code d'invitation (API/Fetch).
     */
    public function verifierCodeInvitation(string $code)
    {
        $codeClean = strtoupper(trim($code));

        // 1. Recherche dans les invitations spécifiques créées par l'administrateur avec rôle attribué
        $invitation = Invitation::with(['clinique', 'role'])
            ->where('code', $codeClean)
            ->whereNull('utilise_le')
            ->first();

        if ($invitation) {
            $clinique = $invitation->clinique;
            $role = $invitation->role;
        } else {
            // 2. Recherche dans le code d'équipe général de la clinique
            $clinique = Clinique::where('code_invitation', $codeClean)->first();
            $role = Role::whereIn('nom', ['Collaborateur', 'Réceptionniste'])->first()
                ?? Role::whereNotIn('nom', ['Super Administrateur', 'Admin'])->first();
        }

        if (! $clinique) {
            return response()->json([
                'valide' => false,
                'message' => 'Code introuvable',
            ]);
        }

        $initiales = collect(explode(' ', $clinique->nom))
            ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
            ->take(2)
            ->implode('');

        return response()->json([
            'valide' => true,
            'nom' => $clinique->nom,
            'ville' => $clinique->ville,
            'initiales' => $initiales ?: 'CL',
            'statut' => $clinique->statut,
            'est_active' => $clinique->estActive(),
            'role_id' => $role?->id,
            'role_nom' => $role?->nom ?? 'Collaborateur',
            'prenom' => $invitation?->prenom,
            'nom_famille' => $invitation?->nom,
            'email' => $invitation?->email,
        ]);
    }

    /**
     * Traite l'inscription (Rejoindre une clinique avec code d'invitation ou Créer sa clinique).
     *
     * @return RedirectResponse
     */
    public function register(Request $request)
    {
        $actionType = $request->input('action_type', 'rejoindre');

        // Découpage automatique du Nom complet si fourni
        if ($request->filled('nom_complet') && (! $request->filled('nom') || ! $request->filled('prenom'))) {
            $parts = explode(' ', trim((string) $request->input('nom_complet')), 2);
            $request->merge([
                'prenom' => $parts[0] ?? 'Utilisateur',
                'nom' => $parts[1] ?? ($parts[0] ?? 'Clinique'),
            ]);
        }

        // Auto-remplissage de confirmation de mot de passe si un seul champ est présent
        if (! $request->filled('password_confirmation') && $request->filled('password')) {
            $request->merge(['password_confirmation' => $request->input('password')]);
        }

        // SCÉNARIO 1 : CRÉER UNE NOUVELLE CLINIQUE (Fondateur / Directeur de clinique)
        if ($actionType === 'creer') {
            $validated = $request->validate([
                'nom_clinique' => 'required|string|max:255',
                'ville' => 'required|string|max:100',
                'pays' => 'nullable|string|max:100',
                'telephone_clinique' => 'nullable|string|max:50',
                'type_etablissement' => 'nullable|string|max:50',
                'nom' => 'required|string|max:100',
                'prenom' => 'required|string|max:100',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ], [
                'nom_clinique.required' => 'Le nom de votre clinique est obligatoire.',
                'ville.required' => 'La ville de la clinique est obligatoire.',
                'nom.required' => 'Votre nom de famille est obligatoire.',
                'prenom.required' => 'Votre prénom est obligatoire.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            ]);

            $user = DB::transaction(function () use ($validated) {
                $codePrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['nom_clinique']), 0, 4)) ?: 'CLI';
                $codeInvitation = $codePrefix.'-'.rand(1000, 9999);

                while (Clinique::where('code_invitation', $codeInvitation)->exists()) {
                    $codeInvitation = $codePrefix.'-'.rand(1000, 9999);
                }

                $clinique = Clinique::create([
                    'nom' => $validated['nom_clinique'],
                    'type_etablissement' => $validated['type_etablissement'] ?? 'Policlinique',
                    'ville' => $validated['ville'],
                    'pays' => $validated['pays'] ?? 'Mali',
                    'telephone' => $validated['telephone_clinique'] ?? null,
                    'devise' => 'FCFA',
                    'statut' => 'actif',
                    'prefixe_ticket' => 'TCK',
                    'prefixe_patient' => 'PAT',
                    'code_invitation' => $codeInvitation,
                ]);

                $adminRole = Role::firstOrCreate(
                    ['nom' => 'Administrateur'],
                    ['description' => 'Administration complète de la clinique']
                );

                return User::create([
                    'clinique_id' => $clinique->id,
                    'nom' => $validated['nom'],
                    'prenom' => $validated['prenom'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role_id' => $adminRole->id,
                ]);
            });

            Auth::login($user);

            return redirect()->route('dashboard')->with('alert', "Félicitations ! Votre clinique « {$user->clinique->nom} » a été créée avec succès. Votre code d'invitation pour votre équipe est : {$user->clinique->code_invitation}.");
        }

        // SCÉNARIO 2 : REJOINDRE UNE CLINIQUE EXISTANTE AVEC CODE D'INVITATION
        $codeClean = strtoupper(trim((string) $request->input('code_invitation', '')));
        $request->merge(['code_invitation' => $codeClean]);

        // Vérifier si une invitation spécifique avec rôle fixé existe
        $invitation = Invitation::with(['clinique', 'role'])
            ->where('code', $codeClean)
            ->whereNull('utilise_le')
            ->first();

        if ($invitation) {
            $clinique = $invitation->clinique;
            $assignedRoleId = $invitation->role_id;
            $request->merge(['role_id' => $assignedRoleId]);
        } else {
            $clinique = Clinique::where('code_invitation', $codeClean)->first();
            if (! $request->filled('role_id')) {
                $defaultRole = Role::whereIn('nom', ['Collaborateur', 'Réceptionniste'])->first()
                    ?? Role::whereNotIn('nom', ['Super Administrateur', 'Admin'])->first();
                if ($defaultRole) {
                    $request->merge(['role_id' => $defaultRole->id]);
                }
            }
            $assignedRoleId = $request->input('role_id');
        }

        $validated = $request->validate([
            'code_invitation' => 'required|string',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'code_invitation.required' => 'Le code d\'invitation de votre clinique est obligatoire.',
            'nom.required' => 'Le nom de famille est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role_id.required' => 'Le rôle associé à votre invitation est obligatoire.',
        ]);

        if (! $clinique) {
            return back()->withInput()->withErrors([
                'code_invitation' => "Le code d'invitation « {$codeClean} » est introuvable. Veuillez vérifier auprès de l'administrateur de votre établissement.",
            ])->with('error_tab', 'Rejoindre');
        }

        if (! $clinique->estActive()) {
            return back()->withInput()->withErrors([
                'code_invitation' => "L'accès à la clinique « {$clinique->nom} » est actuellement suspendu. Veuillez contacter votre administration.",
            ])->with('error_tab', 'Rejoindre');
        }

        // Sécurité stricte : si invitation spécifique, le rôle est forcé depuis la base de données
        $finalRoleId = $invitation ? $invitation->role_id : $validated['role_id'];

        $user = User::create([
            'clinique_id' => $clinique->id,
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $finalRoleId,
        ]);

        if ($invitation) {
            $invitation->update([
                'utilise_le' => now(),
                'utilise_par_user_id' => $user->id,
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('alert', "Bienvenue dans l'équipe de « {$clinique->nom} » ! Votre compte est activé avec le profil {$user->role->nom}.");
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
