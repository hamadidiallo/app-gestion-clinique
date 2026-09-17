<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssuranceRequest extends FormRequest
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
     * Définit les règles de validation appliquées aux champs de la requête d'assurance.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Récupère l'ID de l'assurance en cas de modification pour ignorer le code unique existant
        $assuranceId = $this->route('assurance') ? $this->route('assurance')->id : null;

        return [
            // Le nom de l'assurance est obligatoire et ne doit pas dépasser 255 caractères
            'nom' => 'required|string|max:255',

            // Le code d'assurance est facultatif, mais s'il est renseigné il doit être unique
            'code' => 'nullable|string|max:50|unique:assurances,code,'.$assuranceId,

            // Le numéro de téléphone est facultatif et ne doit pas dépasser 20 caractères
            'telephone' => 'nullable|string|max:20',

            // L'adresse email est facultative mais doit être une adresse email valide si elle est fournie
            'email' => 'nullable|email|max:255',

            // L'adresse physique est facultative et limitée à 500 caractères
            'adresse' => 'nullable|string|max:500',

            // Taux de prise en charge par défaut (%) (ex: 70%, 80%)
            'taux_par_defaut' => 'nullable|numeric|min:0|max:100',

            // Le statut est obligatoire et doit être un booléen (1 pour Actif, 0 pour Inactif)
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages de validation personnalisés en français pour la table assurances.
     */
    public function messages(): array
    {
        return [
            // Messages de validation pour le nom de l'assurance
            'nom.required' => 'Le nom de la compagnie d\'assurance est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères valide.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',

            // Messages de validation pour le code d'assurance
            'code.string' => 'Le code doit être une chaîne de caractères valide.',
            'code.max' => 'Le code ne doit pas dépasser 50 caractères.',
            'code.unique' => 'Ce code d\'assurance est déjà utilisé par une autre compagnie.',

            // Messages de validation pour le téléphone
            'telephone.string' => 'Le numéro de téléphone doit être valide.',
            'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',

            // Messages de validation pour l'email
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.max' => 'L\'adresse email ne doit pas dépasser 255 caractères.',

            // Messages de validation pour l'adresse
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères valide.',
            'adresse.max' => 'L\'adresse ne doit pas dépasser 500 caractères.',

            // Messages de validation pour le statut
            'statut.required' => 'Le statut de l\'assurance est obligatoire.',
            'statut.boolean' => 'Le statut doit être valide (Actif ou Inactif).',
        ];
    }
}
