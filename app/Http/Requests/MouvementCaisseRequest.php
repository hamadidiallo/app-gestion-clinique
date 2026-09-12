<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MouvementCaisseRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les mouvements d'espèces en caisse.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // La caisse associée est obligatoire
            'caisse_id' => 'required|exists:caisses,id',

            // L'utilisateur agent est obligatoire
            'user_id' => 'required|exists:users,id',

            // Type de mouvement (entree ou sortie)
            'type' => 'required|string|max:50',

            // Origine ou motif (ex: paiement_ticket, alimentation, retrait, depense)
            'origine' => 'required|string|max:100',

            // Référence du mouvement
            'reference' => 'required|string|max:100',

            // Montant du mouvement
            'montant' => 'required|numeric|min:0',

            // Date et heure
            'date_mouvement' => 'required|date',

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
            'caisse_id.required' => 'La caisse concernée est obligatoire.',
            'user_id.required' => 'L\'agent responsable est obligatoire.',
            'type.required' => 'Le type de mouvement (entrée/sortie) est obligatoire.',
            'reference.required' => 'La référence du mouvement est obligatoire.',
            'montant.required' => 'Le montant du mouvement est obligatoire.',
        ];
    }
}
