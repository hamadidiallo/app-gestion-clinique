<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaiementRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour un règlement / paiement.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le ticket de caisse rattaché est obligatoire
            'ticket_id' => 'required|exists:tickets,id',

            // L'utilisateur agent/caissier qui enregistre le paiement est obligatoire
            'user_id' => 'required|exists:users,id',

            // L'assurance éventuelle payeuse
            'assurance_id' => 'nullable|exists:assurances,id',

            // Référence du reçu ou transaction
            'reference' => 'required|string|max:100',

            // Type de payeur (ex: patient, assurance, tiers)
            'type_payeur' => 'required|string|max:50',

            // Montants financier du paiement
            'montant_recu' => 'required|numeric|min:0',
            'montant_impute' => 'required|numeric|min:0',
            'montant_rendu' => 'nullable|numeric|min:0',

            // Le mode de paiement est obligatoire (Espèces, Carte, Mobile Money, etc.)
            'mode_paiement_id' => 'required|exists:mode_paiements,id',

            // La date et heure du règlement
            'date_paiement' => 'required|date',

            // Statut du paiement (valide, annule, en_attente)
            'statut' => 'required|string|max:50',

            // Observations optionnelles
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages de validation personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Le ticket parent est obligatoire.',
            'user_id.required' => 'L\'agent caissier est obligatoire.',
            'reference.required' => 'La référence de paiement est obligatoire.',
            'type_payeur.required' => 'Le type de payeur est obligatoire.',
            'montant_recu.required' => 'Le montant reçu est obligatoire.',
            'montant_impute.required' => 'Le montant imputé est obligatoire.',
            'mode_paiement_id.required' => 'Le choix d\'un mode de paiement est obligatoire.',
            'date_paiement.required' => 'La date du règlement est obligatoire.',
        ];
    }
}
