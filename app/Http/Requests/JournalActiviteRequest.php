<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JournalActiviteRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les logs du journal d'activité / audit.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Utilisateur auteur de l'action
            'user_id' => 'required|exists:users,id',

            // Libellé de l'action effectuée (création, modification, suppression, etc.)
            'action' => 'required|string|max:100',

            // Module système concerné
            'module' => 'required|string|max:100',

            // Type et ID d'objet ciblé
            'objet_type' => 'nullable|string|max:255',
            'objet_id' => 'nullable|integer',

            // Description et métadonnées
            'description' => 'nullable|string',
            'anciennes_valeurs' => 'nullable|array',
            'nouvelles_valeurs' => 'nullable|array',

            // Adresse IP et date
            'adresse_ip' => 'nullable|ip',
            'date_action' => 'required|date',
        ];
    }

    /**
     * Messages explicatifs en français.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'action.required' => 'L\'action exécutée est obligatoire.',
            'module.required' => 'Le module est obligatoire.',
            'date_action.required' => 'La date et l\'heure de l\'action sont obligatoires.',
        ];
    }
}
