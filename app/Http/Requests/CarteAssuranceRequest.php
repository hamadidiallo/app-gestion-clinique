<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CarteAssuranceRequest extends FormRequest
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
     * Définit les règles de validation appliquées aux champs de la requête de carte d'assurance.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Récupère l'ID de la carte d'assurance en cas de modification pour ignorer la référence unique existante
        $carteId = $this->route('carteassurance') ? $this->route('carteassurance')->id : null;

        return [
            // Le patient associé est obligatoire et doit exister dans la table patients
            'patient_id' => 'required|exists:patients,id',

            // La compagnie d'assurance associée est obligatoire et doit exister dans la table assurances
            'assurance_id' => 'required|exists:assurances,id',

            // La référence de la carte est obligatoire, unique et limitée à 100 caractères
            'reference' => 'required|string|max:100|unique:carte_assurances,reference,' . $carteId,

            // Le taux de couverture est obligatoire et doit être un nombre entre 0 et 100
            'taux_couverture' => 'required|numeric|min:0|max:100',

            // La date de début de validité est facultative mais doit être une date valide
            'date_debut' => 'nullable|date',

            // La date de fin de validité est facultative, doit être une date et ne peut être antérieure à la date de début
            'date_fin' => 'nullable|date|after_or_equal:date_debut',

            // Le statut est obligatoire et doit être un booléen (1 pour Actif, 0 pour Inactif)
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages de validation personnalisés en français pour les cartes d'assurance.
     */
    public function messages(): array
    {
        return [
            // Messages pour le champ patient_id
            'patient_id.required' => 'Le choix du patient est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas dans la base de données.',

            // Messages pour le champ assurance_id
            'assurance_id.required' => 'Le choix de la compagnie d\'assurance est obligatoire.',
            'assurance_id.exists' => 'La compagnie d\'assurance sélectionnée n\'existe pas dans la base de données.',

            // Messages pour le champ reference
            'reference.required' => 'La référence de la carte d\'assurance est obligatoire.',
            'reference.string' => 'La référence doit être une chaîne de caractères valide.',
            'reference.max' => 'La référence ne doit pas dépasser 100 caractères.',
            'reference.unique' => 'Cette référence de carte est déjà enregistrée.',

            // Messages pour le champ taux_couverture
            'taux_couverture.required' => 'Le taux de couverture est obligatoire.',
            'taux_couverture.numeric' => 'Le taux de couverture doit être un nombre valide.',
            'taux_couverture.min' => 'Le taux de couverture ne peut pas être inférieur à 0%.',
            'taux_couverture.max' => 'Le taux de couverture ne peut pas dépasser 100%.',

            // Messages pour les champs de dates
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',

            // Messages pour le statut
            'statut.required' => 'Le statut de la carte est obligatoire.',
            'statut.boolean' => 'Le statut doit être valide (Actif ou Inactif).',
        ];
    }
}
