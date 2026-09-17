<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RemunerationRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les rémunérations des médecins.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le médecin bénéficiaire est obligatoire
            'medecin_id' => 'required|exists:medecins,id',

            // L'agent gestionnaire qui valide la paie
            'user_id' => 'nullable|exists:users,id',

            // Type de rémunération
            'type_remuneration' => 'nullable|string|max:50',

            // Période
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',

            // Montants et pourcentages (calculés automatiquement si non fournis)
            'salaire_fixe' => 'nullable|numeric|min:0',
            'pourcentage' => 'nullable|numeric|min:0|max:100',
            'montant_base' => 'nullable|numeric|min:0',
            'montant_medecin' => 'nullable|numeric|min:0',
            'montant_clinique' => 'nullable|numeric|min:0',

            // Statut et date de paiement
            'statut' => 'nullable|string|max:50',
            'date_paiement' => 'nullable|date',

            // Notes
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages explicatifs en français.
     */
    public function messages(): array
    {
        return [
            'medecin_id.required' => 'Le médecin destinataire est obligatoire.',
            'user_id.required' => 'L\'agent qui valide la fiche est obligatoire.',
            'periode_debut.required' => 'La date de début de période est obligatoire.',
            'periode_fin.required' => 'La date de fin de période est obligatoire.',
            'montant_base.required' => 'Le montant de base est obligatoire.',
            'montant_medecin.required' => 'Le montant à verser au médecin est obligatoire.',
            'statut.required' => 'Le statut du paiement est obligatoire.',
        ];
    }
}
