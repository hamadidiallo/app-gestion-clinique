<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DepenseRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour l'enregistrement d'une dépense.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Catégorie de dépense obligatoire
            'categorie_depense_id' => 'required|exists:categorie_depenses,id',

            // Mode de paiement obligatoire
            'mode_paiement_id' => 'required|exists:mode_paiements,id',

            // Agent enregistreur obligatoire
            'user_id' => 'required|exists:users,id',

            // Montant de la dépense
            'montant' => 'required|numeric|min:0',

            // Date de la dépense
            'date_depense' => 'required|date',

            // Bénéficiaire / Fournisseur
            'beneficiaire' => 'required|string|max:255',

            // Référence de facture ou pièce justificative
            'reference' => 'required|string|max:100',

            // Description / motif
            'description' => 'nullable|string',

            // Statut
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages explicatifs en français.
     */
    public function messages(): array
    {
        return [
            'categorie_depense_id.required' => 'Le choix d\'une catégorie de dépense est obligatoire.',
            'mode_paiement_id.required' => 'Le mode de paiement est obligatoire.',
            'user_id.required' => 'L\'agent enregistreur est obligatoire.',
            'montant.required' => 'Le montant de la dépense est obligatoire.',
            'date_depense.required' => 'La date de dépense est obligatoire.',
            'beneficiaire.required' => 'Le nom du bénéficiaire ou fournisseur est obligatoire.',
            'reference.required' => 'La référence de pièce justificative est obligatoire.',
        ];
    }
}
