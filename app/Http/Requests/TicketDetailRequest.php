<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TicketDetailRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la ligne de détail de ticket.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le ticket rattaché est obligatoire et doit exister
            'ticket_id' => 'required|exists:tickets,id',

            // La prestation médicale associée est obligatoire
            'prestation_id' => 'required|exists:prestations,id',

            // La quantité est obligatoire et doit être un entier d'au moins 1
            'quantite' => 'required|integer|min:1',

            // Le prix unitaire de la prestation est obligatoire
            'prix_unitaire' => 'required|numeric|min:0',

            // Montant total de la ligne
            'montant_total' => 'required|numeric|min:0',

            // Part assurance et part patient pour cette ligne
            'montant_assurance' => 'nullable|numeric|min:0',
            'montant_patient' => 'required|numeric|min:0',

            // Statut de la ligne
            'statut' => 'required|boolean',

            // Description / observations
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages de validation en français.
     */
    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Le ticket parent est obligatoire.',
            'prestation_id.required' => 'Le choix d\'une prestation médicale est obligatoire.',
            'quantite.required' => 'La quantité est obligatoire.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'montant_total.required' => 'Le montant total de la ligne est obligatoire.',
            'montant_patient.required' => 'Le montant patient est obligatoire.',
        ];
    }
}
