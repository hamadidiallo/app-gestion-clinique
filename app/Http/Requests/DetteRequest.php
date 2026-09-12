<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DetteRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les dettes / restes à recouvrir.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le ticket de facturation est obligatoire
            'ticket_id' => 'required|exists:tickets,id',

            // Le patient est obligatoire
            'patient_id' => 'required|exists:patients,id',

            // L'agent gestionnaire est obligatoire
            'user_id' => 'required|exists:users,id',

            // Montants de la créance
            'montant_initial' => 'required|numeric|min:0',
            'montant_paye' => 'nullable|numeric|min:0',
            'reste_a_payer' => 'required|numeric|min:0',

            // Statut de la dette (ex: en_cours, soldee, douteuse)
            'statut' => 'required|string|max:50',

            // Dates
            'date_creation' => 'required|date',
            'date_reglement' => 'nullable|date|after_or_equal:date_creation',

            // Description
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Le ticket parent est obligatoire.',
            'patient_id.required' => 'Le choix du patient est obligatoire.',
            'montant_initial.required' => 'Le montant initial de la dette est obligatoire.',
            'reste_a_payer.required' => 'Le reste à payer est obligatoire.',
            'statut.required' => 'Le statut de la dette est obligatoire.',
        ];
    }
}
