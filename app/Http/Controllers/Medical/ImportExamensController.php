<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Acte;
use App\Services\ImportExamensService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Import des examens de laboratoire : catalogue de départ intégré ou fichier CSV (export Excel).
 */
class ImportExamensController extends Controller
{
    public function __construct(private readonly ImportExamensService $import) {}

    public function index(): View
    {
        $catalogue = collect($this->import->catalogue());
        $codesExistants = Acte::whereIn('code', $catalogue->pluck('code'))->pluck('code')->all();

        return view('medical.actes.import', [
            'catalogue' => $catalogue,
            'codesExistants' => $codesExistants,
        ]);
    }

    public function catalogue(Request $request): RedirectResponse
    {
        $codesDisponibles = array_column($this->import->catalogue(), 'code');

        $donnees = $request->validate([
            'codes' => ['required', 'array', 'min:1'],
            'codes.*' => ['string', Rule::in($codesDisponibles)],
        ], [
            'codes.required' => 'Cochez au moins un examen à importer.',
        ]);

        $examens = array_values(array_filter(
            $this->import->catalogue(),
            fn (array $examen) => in_array($examen['code'], $donnees['codes'], true)
        ));

        // Catalogue : on complète sans écraser les valeurs que la clinique a déjà ajustées
        $bilan = $this->import->importer($request->user()->clinique, $examens, false);

        return redirect()->route('actes.import')->with('bilan', $bilan);
    }

    public function fichier(Request $request): RedirectResponse
    {
        $request->validate([
            'fichier' => ['required', 'file', 'max:2048', 'mimes:csv,txt'],
        ], [
            'fichier.required' => 'Choisissez le fichier à importer.',
            'fichier.mimes' => 'Le fichier doit être au format CSV. Dans Excel : Fichier › Enregistrer sous › CSV (séparateur : point-virgule).',
        ]);

        ['examens' => $examens, 'erreurs' => $erreurs] = $this->import->lireCsv($request->file('fichier')->getRealPath());

        // Fichier avec erreurs : rien n'est importé, pour ne pas laisser un catalogue à moitié chargé
        if ($erreurs) {
            return redirect()->route('actes.import')->with('erreursImport', $erreurs);
        }

        if (! $examens) {
            return redirect()->route('actes.import')->with('erreursImport', ['Le fichier ne contient aucun examen.']);
        }

        // Fichier de la clinique : ses valeurs remplacent celles déjà enregistrées
        $bilan = $this->import->importer($request->user()->clinique, $examens, true);

        return redirect()->route('actes.import')->with('bilan', $bilan);
    }

    public function modele(): Response
    {
        return response($this->import->modeleCsv(), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modele-examens-medigest.csv"',
        ]);
    }
}
