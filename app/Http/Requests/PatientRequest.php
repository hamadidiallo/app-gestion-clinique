<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autorise tous les utilisateurs à soumettre ce formulaire
        return true;
    }

    /**
     * Définit les règles de validation appliquées aux champs de la requête.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le prénom est obligatoire, doit être une chaîne et ne doit pas dépasser 50 caractères
            'prenom' => 'required|string|max:50',

            // Le nom est obligatoire, doit être une chaîne et ne doit pas dépasser 50 caractères
            'nom' => 'required|string|max:50',

            // Le sexe est obligatoire et doit être soit 'M' soit 'F'
            'sexe' => 'required|in:M,F',

            // Le numéro de téléphone est facultatif (peut être vide), mais s'il est renseigné, il doit être une chaîne de 20 caractères max
            'telephone' => 'nullable|string|max:20',

            // Le statut est obligatoire et doit être soit 'assure' soit 'non_assure'
            'statut' => 'required|in:assure,non_assure',

            // Champs optionnels d'assurance rattachée au patient
            'assurance_id' => 'nullable|exists:assurances,id',
            'numero_assure' => 'nullable|string|max:100',
            'carte_reference' => 'nullable|string|max:100',
            'taux_couverture' => 'nullable|numeric|min:0|max:100',

            // Dossier médical initial et action de redirection
            'groupe_sanguin' => 'nullable|string|max:10',
            'allergies' => 'nullable|string|max:255',
            'antecedents_personnels' => 'nullable|string',
            'action' => 'nullable|string|in:save,save_and_ticket',
        ];
    }

    /**
     * Messages de validation personnalisés en français.
     */
    public function messages(): array
    {
        return [
            // Messages de validation pour le prénom
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères valide.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 50 caractères.',

            // Messages de validation pour le nom
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères valide.',
            'nom.max' => 'Le nom ne doit pas dépasser 50 caractères.',

            // Messages de validation pour le sexe
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe sélectionné doit être M (Masculin) ou F (Féminin).',

            // Messages de validation pour le téléphone
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères valide.',
            'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',

            // Messages de validation pour le statut
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être soit "assure" (Assuré) soit "non_assure" (Non Assuré).',
        ];
    }
}
