<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RecetteRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les recettes de la clinique.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Ticket et paiement associés facultatifs ou obligatoires
            'ticket_id' => 'nullable|exists:tickets,id',
            'paiement_id' => 'nullable|exists:paiements,id',

            // User agent enregistreur
            'user_id' => 'required|exists:users,id',

            // Mode de paiement obligatoire
            'mode_paiement_id' => 'required|exists:mode_paiements,id',

            // Montant de la recette
            'montant' => 'required|numeric|min:0',

            // Date de recette
            'date_recette' => 'required|date',

            // Référence
            'reference' => 'required|string|max:100',

            // Description
            'description' => 'nullable|string',

            // Statut (booléen)
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages explicatifs en français.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'L\'agent enregistreur est obligatoire.',
            'montant.required' => 'Le montant de la recette est obligatoire.',
            'date_recette.required' => 'La date d\'encaissement de la recette est obligatoire.',
            'reference.required' => 'La référence de la recette est obligatoire.',
        ];
    }
}
