<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour un employé.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Le prénom de l'employé est obligatoire
            'prenom' => 'required|string|max:255',

            // Le nom de l'employé est obligatoire
            'nom' => 'required|string|max:255',

            // Le numéro de téléphone est obligatoire
            'telephone' => 'required|string|max:50',

            // L'adresse e-mail est optionnelle mais doit être valide
            'email' => 'nullable|email|max:255',

            // La fonction ou poste est obligatoire
            'fonction' => 'required|string|max:255',

            // Le type de rémunération est obligatoire
            'type_remuneration' => 'required|string|max:50',

            // Le salaire fixe est facultatif et numérique
            'salaire_fixe' => 'nullable|numeric|min:0',

            // Le pourcentage est optionnel
            'pourcentage' => 'nullable|numeric|min:0|max:100',

            // La date d'embauche est facultative mais doit être valide
            'date_embauche' => 'nullable|date',

            // Le statut est obligatoire et booléen
            'statut' => 'required|boolean',

            // Description/remarques optionnelles
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages explicatifs de validation en français.
     */
    public function messages(): array
    {
        return [
            'prenom.required' => 'Le prénom de l\'employé est obligatoire.',
            'nom.required' => 'Le nom de l\'employé est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'email.email' => 'L\'adresse e-mail doit être valide.',
            'fonction.required' => 'La fonction / poste de l\'employé est obligatoire.',
            'type_remuneration.required' => 'Le type de rémunération est obligatoire.',
            'statut.required' => 'Le statut de l\'employé est obligatoire.',
        ];
    }
}
