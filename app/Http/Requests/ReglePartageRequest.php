<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReglePartageRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les règles de partage d'honoraires.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le service médical associé est obligatoire
            'service_id' => 'required|exists:services,id',

            // Pourcentages médecin et clinique
            'pourcentage_medecin' => 'required|numeric|min:0|max:100',
            'pourcentage_clinique' => 'required|numeric|min:0|max:100',

            // Période d'application
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',

            // Statut
            'statut' => 'required|boolean',

            // Description / notes
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages explicatifs en français.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Le service médical est obligatoire.',
            'pourcentage_medecin.required' => 'Le pourcentage du médecin est obligatoire.',
            'pourcentage_clinique.required' => 'Le pourcentage de la clinique est obligatoire.',
            'date_debut.required' => 'La date de début d\'application est obligatoire.',
            'statut.required' => 'Le statut de la règle est obligatoire.',
        ];
    }
}
