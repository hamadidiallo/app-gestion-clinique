<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autorise l'exécution de la requête de validation
        return true;
    }

    /**
     * Définit les règles de validation appliquées au formulaire des rôles.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Récupère l'identifiant du rôle s'il s'agit d'une mise à jour (pour ignorer son propre ID dans unique)
        $roleId = $this->route('role') ? $this->route('role')->id : null;

        return [
            // Le nom est obligatoire, max 50 caractères et unique
            'nom' => 'required|string|max:50|unique:roles,nom,'.$roleId,

            // La description est facultative
            'description' => 'nullable|string',
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.max' => 'Le nom ne doit pas dépasser 50 caractères.',
            'nom.unique' => 'Ce nom de rôle est déjà utilisé.',
        ];
    }
}
