<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ModePaiementRequest extends FormRequest
{
    /**
     * Autorise cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour un mode de paiement.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $modePaiementId = $this->route('modepaiement') ? $this->route('modepaiement')->id : null;

        return [
            // Le nom du mode de paiement est obligatoire
            'nom' => 'required|string|max:255',

            // Le code unique du mode de paiement est obligatoire
            'code' => 'required|string|max:50|unique:mode_paiements,code,'.$modePaiementId,

            // Description optionnelle
            'description' => 'nullable|string',

            // Statut obligatoire (booléen)
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages de validation en français.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du mode de paiement est obligatoire.',
            'code.required' => 'Le code du mode de paiement est obligatoire.',
            'code.unique' => 'Ce code de mode de paiement existe déjà.',
            'statut.required' => 'Le statut du mode de paiement est obligatoire.',
        ];
    }
}
