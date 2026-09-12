<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autorise tous les utilisateurs à soumettre ce formulaire
        return true;
    }

    /**
     * Définit les règles de validation appliquées aux champs de la requête de service.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Récupère l'ID du service en cas de modification pour ignorer le code unique existant
        $serviceId = $this->route('service') ? $this->route('service')->id : null;

        return [
            // Le nom du service est obligatoire et ne doit pas dépasser 255 caractères
            'nom' => 'required|string|max:255',

            // Le code du service est obligatoire et doit être unique dans la table services
            'code' => 'required|string|max:50|unique:services,code,' . $serviceId,

            // La description du service est facultative
            'description' => 'nullable|string',

            // Le statut est obligatoire et doit être un booléen (1 pour Actif, 0 pour Inactif)
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages de validation personnalisés en français pour la table services.
     */
    public function messages(): array
    {
        return [
            // Messages de validation pour le nom du service
            'nom.required' => 'Le nom du service est obligatoire.',
            'nom.string' => 'Le nom du service doit être une chaîne de caractères valide.',
            'nom.max' => 'Le nom du service ne doit pas dépasser 255 caractères.',

            // Messages de validation pour le code du service
            'code.required' => 'Le code du service est obligatoire.',
            'code.string' => 'Le code du service doit être une chaîne de caractères valide.',
            'code.max' => 'Le code du service ne doit pas dépasser 50 caractères.',
            'code.unique' => 'Ce code de service est déjà attribué à un autre service.',

            // Messages de validation pour la description
            'description.string' => 'La description doit être un texte valide.',

            // Messages de validation pour le statut
            'statut.required' => 'Le statut du service est obligatoire.',
            'statut.boolean' => 'Le statut doit être valide (Actif ou Inactif).',
        ];
    }
}
