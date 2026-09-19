<?php

namespace App\Http\Controllers;

use App\Models\Clinique;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ParametreController extends Controller
{
    /**
     * Affiche l'écran des paramètres de l'établissement / clinique.
     */
    public function index(): View
    {
        $clinique = auth()->user()->clinique;

        return view('parametres.index', compact('clinique'));
    }

    /**
     * Enregistre les modifications apportées aux paramètres de la clinique.
     */
    public function update(Request $request): RedirectResponse
    {
        $clinique = auth()->user()->clinique;

        if (! $clinique) {
            return back()->with('error', 'Aucune clinique associée à votre compte.');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type_etablissement' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'ville' => 'required|string|max:100',
            'pays' => 'required|string|max:100',
            'devise' => 'required|string|max:10',
            'prefixe_ticket' => 'required|string|max:10',
            'prefixe_patient' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ], [
            'nom.required' => 'Le nom de la clinique est obligatoire.',
            'ville.required' => 'La ville est obligatoire.',
            'pays.required' => 'Le pays est obligatoire.',
            'devise.required' => 'La devise monétaire est obligatoire.',
            'prefixe_ticket.required' => 'Le préfixe des tickets est obligatoire.',
            'prefixe_patient.required' => 'Le préfixe des dossiers patients est obligatoire.',
            'logo.image' => 'Le logo doit être une image valide.',
            'logo.mimes' => 'Le logo doit être au format jpeg, png, jpg, svg ou webp.',
            'logo.max' => 'La taille du logo ne doit pas dépasser 2 Mo.',
        ]);

        // Gestion de la suppression du logo existant si demandée
        if ($request->boolean('supprimer_logo') && $clinique->logo) {
            Storage::disk('public')->delete($clinique->logo);
            $clinique->logo = null;
        }

        // Gestion de l'upload d'un nouveau logo
        if ($request->hasFile('logo')) {
            if ($clinique->logo) {
                Storage::disk('public')->delete($clinique->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $clinique->logo = $logoPath;
        }

        $clinique->nom = $validated['nom'];
        $clinique->type_etablissement = $validated['type_etablissement'] ?? $clinique->type_etablissement;
        $clinique->email = $validated['email'] ?? null;
        $clinique->telephone = $validated['telephone'] ?? null;
        $clinique->adresse = $validated['adresse'] ?? null;
        $clinique->ville = $validated['ville'];
        $clinique->pays = $validated['pays'];
        $clinique->devise = strtoupper(trim($validated['devise']));
        $clinique->prefixe_ticket = strtoupper(trim($validated['prefixe_ticket']));
        $clinique->prefixe_patient = strtoupper(trim($validated['prefixe_patient']));
        $clinique->save();

        return redirect()->route('parametres.index')->with('alert', 'Les informations et paramètres de votre clinique ont été mis à jour avec succès.');
    }

    /**
     * Génère un nouveau code d'invitation pour la clinique.
     */
    public function regenererCodeInvitation(): RedirectResponse
    {
        $clinique = auth()->user()->clinique;

        if (! $clinique) {
            return back()->with('error', 'Clinique introuvable.');
        }

        $codePrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $clinique->nom), 0, 4)) ?: 'CLI';
        $nouveauCode = $codePrefix.'-'.rand(1000, 9999);

        while (Clinique::where('code_invitation', $nouveauCode)->where('id', '!=', $clinique->id)->exists()) {
            $nouveauCode = $codePrefix.'-'.rand(1000, 9999);
        }

        $clinique->update(['code_invitation' => $nouveauCode]);

        return redirect()->route('parametres.index')->with('alert', "Nouveau code d'invitation généré : {$nouveauCode}. Vos collaborateurs peuvent désormais l'utiliser pour rejoindre votre clinique.");
    }
}
