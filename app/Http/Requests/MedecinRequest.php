<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MedecinRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autorise tous les utilisateurs à soumettre le formulaire de médecin
        return true;
    }

    /**
     * Définit les règles de validation appliquées aux champs du médecin.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le nom du médecin est obligatoire, texte de max 255 caractères
            'nom' => 'required|string|max:255',

            // Le prénom du médecin est obligatoire, texte de max 255 caractères
            'prenom' => 'required|string|max:255',

            // Le numéro de téléphone est obligatoire
            'telephone' => 'required|string|max:50',

            // La spécialité médicale est obligatoire
            'specialite' => 'required|string|max:255',

            // Le type de rémunération est obligatoire (ex: pourcentage, fixe, mixte)
            'type_remuneration' => 'required|string|max:50',

            // Le pourcentage de rétrocession est facultatif, numérique entre 0 et 100
            'pourcentage' => 'nullable|numeric|min:0|max:100',

            // Le salaire fixe est facultatif, numérique et positif
            'salaire_fixe' => 'nullable|numeric|min:0',

            // Le statut du médecin est obligatoire et booléen (1 = Actif, 0 = Inactif)
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages de validation personnalisés en français pour les médecins.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du médecin est obligatoire.',
            'prenom.required' => 'Le prénom du médecin est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'specialite.required' => 'La spécialité médicale est obligatoire.',
            'type_remuneration.required' => 'Le type de rémunération est obligatoire.',
            'pourcentage.numeric' => 'Le pourcentage doit être une valeur numérique.',
            'pourcentage.max' => 'Le pourcentage ne peut pas dépasser 100%.',
            'salaire_fixe.numeric' => 'Le salaire fixe doit être un montant numérique.',
            'statut.required' => 'Le statut du médecin est obligatoire.',
        ];
    }
}
