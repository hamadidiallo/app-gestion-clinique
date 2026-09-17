<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategorieDepenseRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour les catégories de dépenses.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categorieId = $this->route('categoriedepense') ? $this->route('categoriedepense')->id : null;

        return [
            // Intitulé de la catégorie
            'nom' => 'required|string|max:255',

            // Code unique
            'code' => 'required|string|max:50|unique:categorie_depenses,code,'.$categorieId,

            // Description optionnelle
            'description' => 'nullable|string',

            // Statut (booléen)
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages de validation en français.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la catégorie de dépense est obligatoire.',
            'code.required' => 'Le code de la catégorie est obligatoire.',
            'code.unique' => 'Ce code de catégorie existe déjà.',
            'statut.required' => 'Le statut de la catégorie est obligatoire.',
        ];
    }
}
