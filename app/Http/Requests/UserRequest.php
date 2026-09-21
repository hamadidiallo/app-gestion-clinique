<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * Définit les règles de validation appliquées aux champs de la requête.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    /**
     * Normalise le téléphone avant validation.
     *
     * La règle d'unicité doit porter sur la forme réellement stockée en base,
     * sinon deux écritures du même numéro passeraient la validation avant de
     * heurter l'index unique.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('telephone')) {
            $this->merge(['telephone' => User::normaliserTelephone($this->input('telephone'))]);
        }
    }

    public function rules(): array
    {
        // Récupère l'ID de l'utilisateur de la route en cas de modification (ex: /users/{user})
        $userId = $this->route('user') ? $this->route('user')->id : null;

        // Si la requête est en POST (création), le mot de passe est obligatoire ; en PUT/PATCH (édition), il est facultatif
        $passwordRule = $this->isMethod('post') ? 'required|string|min:4' : 'nullable|string|min:4';

        return [
            // Le prénom est obligatoire et ne doit pas dépasser 50 caractères
            'prenom' => 'required|string|max:50',

            // Le nom est obligatoire et ne doit pas dépasser 50 caractères
            'nom' => 'required|string|max:50',

            // L'email est obligatoire, valide et unique (en ignorant l'ID actuel en cas d'édition)
            'email' => 'required|email|unique:users,email,'.$userId,

            // Le téléphone sert à se connecter : unique, en ignorant le compte courant
            'telephone' => 'required|string|max:30|unique:users,telephone,'.$userId,

            // Règle du mot de passe adaptée selon création ou modification
            'password' => $passwordRule,

            // Le rôle est obligatoire et doit exister dans la table roles
            'role_id' => 'required|exists:roles,id',
        ];
    }

    /**
     * Messages de validation personnalisés en français.
     */
    public function messages(): array
    {
        return [
            // Messages de validation pour le prénom
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 50 caractères.',

            // Messages de validation pour le nom
            'nom.required' => 'Le nom est obligatoire.',
            'nom.max' => 'Le nom ne doit pas dépasser 50 caractères.',

            // Messages de validation pour l'email
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être une adresse valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire : il sert à se connecter.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà rattaché à un compte.',

            // Messages de validation pour le mot de passe
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 4 caractères.',

            // Messages de validation pour le rôle
            'role_id.required' => 'Veuillez sélectionner un rôle.',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas.',
        ];
    }
}
