<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Acte;
use App\Models\ActeParametre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Paramètres d'un examen de laboratoire (unités et valeurs normales),
 * réglés par l'administration ou le laboratoire.
 */
class ActeParametreController extends Controller
{
    private const CHAMPS_BORNES = [
        'valeur_min', 'valeur_max',
        'valeur_min_homme', 'valeur_max_homme',
        'valeur_min_femme', 'valeur_max_femme',
        'valeur_min_enfant', 'valeur_max_enfant',
    ];

    public function store(Request $request, Acte $acte): RedirectResponse
    {
        abort_unless($acte->estExamenBiologie(), 404);

        $donnees = $this->valider($request);
        $donnees['ordre'] = ($acte->parametres()->max('ordre') ?? 0) + 1;

        $acte->parametres()->create($donnees);

        return redirect()->route('actes.show', $acte)->with('alert', 'Paramètre « '.$donnees['nom'].' » ajouté.');
    }

    public function update(Request $request, Acte $acte, ActeParametre $parametre): RedirectResponse
    {
        abort_unless((int) $parametre->acte_id === (int) $acte->id, 404);

        $parametre->update($this->valider($request));

        return redirect()->route('actes.show', $acte)->with('alert', 'Paramètre « '.$parametre->nom.' » mis à jour.');
    }

    public function destroy(Acte $acte, ActeParametre $parametre): RedirectResponse
    {
        abort_unless((int) $parametre->acte_id === (int) $acte->id, 404);

        $parametre->delete();

        return redirect()->route('actes.show', $acte)->with('alert', 'Paramètre supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request): array
    {
        // Virgule décimale française acceptée (« 12,5 » → 12.5)
        foreach (self::CHAMPS_BORNES as $champ) {
            if (is_string($request->input($champ))) {
                $request->merge([$champ => str_replace([' ', ','], ['', '.'], trim($request->input($champ)))]);
            }
        }

        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in([ActeParametre::TYPE_NUMERIQUE, ActeParametre::TYPE_TEXTE])],
            'unite' => ['nullable', 'string', 'max:30'],
            'valeur_min' => ['nullable', 'numeric', 'required_if:type,numerique'],
            'valeur_max' => ['nullable', 'numeric', 'gte:valeur_min'],
            'valeur_min_homme' => ['nullable', 'numeric'],
            'valeur_max_homme' => ['nullable', 'numeric', 'gte:valeur_min_homme'],
            'valeur_min_femme' => ['nullable', 'numeric'],
            'valeur_max_femme' => ['nullable', 'numeric', 'gte:valeur_min_femme'],
            'valeur_min_enfant' => ['nullable', 'numeric'],
            'valeur_max_enfant' => ['nullable', 'numeric', 'gte:valeur_min_enfant'],
            'valeur_normale_texte' => ['nullable', 'string', 'max:120'],
        ], [
            'nom.required' => 'Indiquez le nom du paramètre (ex. Hémoglobine).',
            'valeur_min.required_if' => 'Indiquez au moins la valeur normale minimale pour un paramètre chiffré.',
            'valeur_max.gte' => 'La valeur maximale doit être supérieure ou égale à la minimale.',
            'valeur_max_homme.gte' => 'Homme : la valeur maximale doit être supérieure ou égale à la minimale.',
            'valeur_max_femme.gte' => 'Femme : la valeur maximale doit être supérieure ou égale à la minimale.',
            'valeur_max_enfant.gte' => 'Enfant : la valeur maximale doit être supérieure ou égale à la minimale.',
        ]);

        // Un paramètre texte n'a pas de bornes chiffrées, et inversement
        if ($donnees['type'] === ActeParametre::TYPE_TEXTE) {
            foreach (self::CHAMPS_BORNES as $champ) {
                $donnees[$champ] = null;
            }
            $donnees['unite'] = null;
        } else {
            $donnees['valeur_normale_texte'] = null;
        }

        return $donnees;
    }
}
