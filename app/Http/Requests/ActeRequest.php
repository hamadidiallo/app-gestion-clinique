<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ActeRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Définit les règles de validation appliquées aux champs de la requête.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $acteId = $this->route('acte') ? $this->route('acte')->id : null;

        return [
            'service_id' => 'required|exists:services,id',
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:actes,code,'.$acteId,
            'categorie' => 'required|string|in:consultation,chirurgie,imagerie,biologie,soins,exploration,maternite,autre',
            'tarif_normal' => 'required|numeric|min:0',
            'tarif_amo' => 'nullable|numeric|min:0',
            'tarif_specifique' => 'nullable|numeric|min:0',
            'part_medecin_pourcentage' => 'required|numeric|min:0|max:100',
            'part_clinique_pourcentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'statut' => 'required|boolean',
        ];
    }

    /**
     * Messages de validation personnalisés en français.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Le service de rattachement est obligatoire.',
            'service_id.exists' => 'Le service sélectionné est invalide.',
            'nom.required' => "Le libellé de l'acte est obligatoire.",
            'nom.string' => "Le libellé de l'acte doit être une chaîne de caractères valide.",
            'nom.max' => "Le libellé de l'acte ne doit pas dépasser 255 caractères.",
            'code.required' => "Le code de l'acte est obligatoire.",
            'code.unique' => 'Ce code est déjà attribué à un autre acte.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'categorie.in' => 'La catégorie sélectionnée est invalide.',
            'tarif_normal.required' => 'Le tarif standard est obligatoire.',
            'tarif_normal.numeric' => 'Le tarif standard doit être un nombre valide.',
            'tarif_normal.min' => 'Le tarif standard ne peut pas être négatif.',
            'tarif_amo.numeric' => 'Le tarif AMO doit être un nombre valide.',
            'tarif_specifique.numeric' => 'Le tarif conventionné spécifique doit être un nombre valide.',
            'part_medecin_pourcentage.required' => 'Le pourcentage du médecin est obligatoire.',
            'part_medecin_pourcentage.numeric' => 'Le pourcentage du médecin doit être une valeur numérique.',
            'part_medecin_pourcentage.min' => 'Le pourcentage du médecin doit être entre 0 et 100.',
            'part_medecin_pourcentage.max' => 'Le pourcentage du médecin doit être entre 0 et 100.',
            'part_clinique_pourcentage.required' => 'Le pourcentage de la clinique est obligatoire.',
            'part_clinique_pourcentage.numeric' => 'Le pourcentage de la clinique doit être une valeur numérique.',
            'part_clinique_pourcentage.min' => 'Le pourcentage de la clinique doit être entre 0 et 100.',
            'part_clinique_pourcentage.max' => 'Le pourcentage de la clinique doit être entre 0 et 100.',
            'statut.required' => "Le statut de l'acte est obligatoire.",
            'statut.boolean' => 'Le statut doit être valide.',
        ];
    }
}
