<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PrestationRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        // Autorise tous les utilisateurs à soumettre ce formulaire
        return true;
    }

    /**
     * Définit les règles de validation appliquées aux champs de la requête de prestation.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // L'identifiant du patient est obligatoire et doit exister dans la table patients
            'patient_id' => 'required|exists:patients,id',

            // L'identifiant du service médical est obligatoire et doit exister dans la table services
            'service_id' => 'required|exists:services,id',

            // L'identifiant du tarif (automatiquement recherché si omis)
            'tarif_id' => 'nullable|exists:tarifs,id',

            // L'identifiant du médecin est optionnel mais doit exister dans la table medecins s'il est renseigné
            'medecin_id' => 'nullable|exists:medecins,id',

            // Le type ou catégorie de la prestation est facultatif (ex: Consultation, Urgence)
            'type' => 'nullable|string|max:255',

            // Le montant de la prestation (calculé automatiquement d'après le tarif si omis)
            'montant' => 'nullable|numeric|min:0',

            // La date et l'heure de réalisation de la prestation sont facultatives (par défaut maintenant)
            'date_prestation' => 'nullable|date',

            // La description ou observations sur la prestation sont facultatives
            'description' => 'nullable|string',

            // Le statut de la prestation (par défaut 1/actif si omis)
            'statut' => 'nullable|boolean',
        ];
    }

    /**
     * Messages de validation personnalisés en français pour la table prestations.
     */
    public function messages(): array
    {
        return [
            // Messages de validation pour le patient
            'patient_id.required' => 'Le choix d\'un patient est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas dans la base de données.',

            // Messages de validation pour le service médical
            'service_id.required' => 'Le choix d\'un service médical est obligatoire.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas dans la base de données.',

            // Messages de validation pour le tarif
            'tarif_id.required' => 'Le choix d\'un tarif applicable est obligatoire.',
            'tarif_id.exists' => 'Le tarif sélectionné n\'existe pas dans la base de données.',

            // Messages de validation pour le médecin
            'medecin_id.exists' => 'Le médecin sélectionné n\'existe pas dans la base de données.',

            // Messages de validation pour le type de prestation
            'type.string' => 'Le type de prestation doit être une chaîne de caractères valide.',
            'type.max' => 'Le type de prestation ne doit pas dépasser 255 caractères.',

            // Messages de validation pour le montant
            'montant.required' => 'Le montant de la prestation est obligatoire.',
            'montant.numeric' => 'Le montant doit être une valeur numérique.',
            'montant.min' => 'Le montant ne peut pas être négatif.',

            // Messages de validation pour la date de prestation
            'date_prestation.required' => 'La date et heure de la prestation sont obligatoires.',
            'date_prestation.date' => 'Veuillez saisir une date et heure valides.',

            // Messages de validation pour la description et le statut
            'description.string' => 'La description doit être un texte valide.',
            'statut.required' => 'Le statut de la prestation est obligatoire.',
            'statut.boolean' => 'Le statut doit être valide (Actif ou Inactif).',
        ];
    }
}
