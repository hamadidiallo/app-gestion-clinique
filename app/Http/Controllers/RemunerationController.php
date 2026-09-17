<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemunerationRequest;
use App\Models\Medecin;
use App\Models\Remuneration;
use App\Models\User;
use App\Services\RemunerationService;
use App\Traits\HasPeriodFilter;

class RemunerationController extends Controller
{
    use HasPeriodFilter;

    public function __construct(
        protected RemunerationService $remunerationService
    ) {}

    /**
     * Affiche la liste des fiches de paie et rétrocessions d'honoraires médicales filtrées par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Remuneration::with(['medecin', 'user'])->latest('periode_debut');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'periode_debut');

        $remunerations = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('remunerations.index', compact('remunerations', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Formulaire de calcul / génération d'une rémunération.
     */
    public function create()
    {
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            'calculee' => 'Calculée / En attente',
            'payee' => 'Payée / Réglée',
            'annulee' => 'Annulée',
        ];

        return view('remunerations.create', compact('medecins', 'users', 'statuts'));
    }

    /**
     * Génère la rémunération mensuelle via le service métier.
     */
    public function store(RemunerationRequest $request)
    {
        $validated = $request->validated();

        try {
            $remuneration = $this->remunerationService->genererRemunerationMensuelle(
                medecinId: (int) $validated['medecin_id'],
                periodeDebut: $validated['periode_debut'],
                periodeFin: $validated['periode_fin'],
                userId: ! empty($validated['user_id']) ? (int) $validated['user_id'] : null
            );

            return to_route('remunerations.show', $remuneration)->with('alert', 'Rémunération calculée automatiquement avec succès ('.$remuneration->montant_medecin.' FBU).');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['medecin_id' => $e->getMessage()]);
        }
    }

    /**
     * Détails d'une rémunération médicale.
     */
    public function show(Remuneration $remuneration)
    {
        $remuneration->load(['medecin', 'user']);

        return view('remunerations.show', compact('remuneration'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Remuneration $remuneration)
    {
        $medecins = Medecin::orderBy('nom')->get();
        $users = User::orderBy('nom')->get();

        $statuts = [
            'calculee' => 'Calculée / En attente',
            'payee' => 'Payée / Réglée',
            'annulee' => 'Annulée',
        ];

        return view('remunerations.edit', compact('remuneration', 'medecins', 'users', 'statuts'));
    }

    /**
     * Met à jour la rémunération.
     */
    public function update(RemunerationRequest $request, Remuneration $remuneration)
    {
        $validated = $request->validated();

        if (isset($validated['statut']) && $validated['statut'] === 'payee' && $remuneration->statut !== 'payee') {
            $this->remunerationService->validerPaiementRemuneration($remuneration);

            return to_route('remunerations.index')->with('alert', 'Paiement de la rémunération validé avec succès.');
        }

        $remuneration->update($validated);

        return to_route('remunerations.index')->with('alert', 'Fiche de rémunération mise à jour avec succès.');
    }

    /**
     * Supprime la fiche de rémunération.
     */
    public function destroy(Remuneration $remuneration)
    {
        if ($remuneration->statut === 'payee') {
            return back()->with('alert', 'Impossible de supprimer une rémunération déjà payée.');
        }

        $remuneration->delete();

        return to_route('remunerations.index')->with('alert', 'Fiche de rémunération supprimée avec succès.');
    }
}
