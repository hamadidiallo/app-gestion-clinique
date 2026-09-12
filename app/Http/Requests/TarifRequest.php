<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TarifRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autorise tous les utilisateurs à effectuer cette requête
        return true;
    }

    /**
     * Définit les règles de validation appliquées aux champs de la requête de tarif.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // L'identifiant du service est obligatoire et doit exister dans la table services
            'service_id' => 'required|exists:services,id',

            // Le tarif normal est obligatoire, numérique et positif
            'tarif_normal' => 'required|numeric|min:0',

            // Le tarif AMO est facultatif, numérique et positif
            'tarif_amo' => 'nullable|numeric|min:0',

            // Le tarif spécifique est facultatif, numérique et positif
            'tarif_specifique' => 'nullable|numeric|min:0',

            // La date de début d'application du tarif est facultative mais doit être une date valide
            'date_debut' => 'nullable|date',

            // La date de fin doit être une date valide et postérieure ou égale à la date de début
            'date_fin' => 'nullable|date|after_or_equal:date_debut',

            // Le statut du tarif est obligatoire et doit être un booléen (1 ou 0)
            'statut' => 'required|boolean',

            // La description ou observation sur le tarif est facultative
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages de validation personnalisés en français pour la table tarifs.
     */
    public function messages(): array
    {
        return [
            // Messages de validation pour le service associé
            'service_id.required' => 'Le choix d\'un service médical est obligatoire.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas dans la base de données.',

            // Messages de validation pour le tarif normal
            'tarif_normal.required' => 'Le tarif normal est obligatoire.',
            'tarif_normal.numeric' => 'Le tarif normal doit être une valeur numérique.',
            'tarif_normal.min' => 'Le tarif normal ne peut pas être négatif.',

            // Messages de validation pour le tarif AMO
            'tarif_amo.numeric' => 'Le tarif AMO doit être une valeur numérique.',
            'tarif_amo.min' => 'Le tarif AMO ne peut pas être négatif.',

            // Messages de validation pour le tarif spécifique
            'tarif_specifique.numeric' => 'Le tarif spécifique doit être une valeur numérique.',
            'tarif_specifique.min' => 'Le tarif spécifique ne peut pas être négatif.',

            // Messages de validation pour les dates de validité
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after_or_equal' => 'La date de fin doit être égale ou postérieure à la date de début.',

            // Messages de validation pour le statut et la description
            'statut.required' => 'Le statut du tarif est obligatoire.',
            'statut.boolean' => 'Le statut doit être valide (Actif ou Inactif).',
            'description.string' => 'La description doit être un texte valide.',
        ];
    }
}
