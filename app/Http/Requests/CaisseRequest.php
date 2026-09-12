<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CaisseRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les sessions de caisses.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le caissier/utilisateur responsable est obligatoire
            'user_id' => 'required|exists:users,id',

            // Date d'ouverture et de fermeture
            'date_ouverture' => 'required|date',
            'date_fermeture' => 'nullable|date|after_or_equal:date_ouverture',

            // Montants de gestion de caisse
            'fonds_initial' => 'required|numeric|min:0',
            'total_entrees' => 'nullable|numeric|min:0',
            'total_sorties' => 'nullable|numeric|min:0',
            'solde_theorique' => 'nullable|numeric',
            'solde_physique' => 'nullable|numeric',
            'ecart' => 'nullable|numeric',

            // Statut de la caisse (ex: ouverte, fermee, verifiee)
            'statut' => 'required|string|max:50',

            // Observations du caissier
            'observation' => 'nullable|string',
        ];
    }

    /**
     * Messages explicatifs en français.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Le caissier responsable est obligatoire.',
            'date_ouverture.required' => 'La date d\'ouverture de la caisse est obligatoire.',
            'fonds_initial.required' => 'Le fond de caisse initial est obligatoire.',
            'statut.required' => 'Le statut de la caisse est obligatoire.',
        ];
    }
}
