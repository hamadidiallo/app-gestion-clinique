<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaisseRequest;
use App\Models\Caisse;
use App\Models\User;
use App\Services\GestionCaisseService;
use App\Traits\HasPeriodFilter;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    use HasPeriodFilter;

    public function __construct(
        protected GestionCaisseService $caisseService
    ) {}

    /**
     * Affiche l'ensemble des sessions de caisses (Ouvertes & Fermées) filtrées par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Caisse::with('user')->latest('date_ouverture');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_ouverture');

        $caisses = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('caisses.index', compact('caisses', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Formulaire d'ouverture d'une nouvelle session de caisse.
     */
    public function create()
    {
        $users = User::orderBy('nom')->get();

        $statuts = [
            'ouverte' => 'Ouverte',
            'fermee' => 'Fermée',
        ];

        return view('caisses.create', compact('users', 'statuts'));
    }

    /**
     * Ouvre et enregistre une session de caisse via GestionCaisseService.
     */
    public function store(CaisseRequest $request)
    {
        $validated = $request->validated();

        try {
            $userId = (int) ($validated['user_id'] ?? auth()->id());
            $fondsInitial = (float) ($validated['fonds_initial'] ?? 0);
            $observation = $validated['observation'] ?? null;

            $caisse = $this->caisseService->ouvrirCaisse($userId, $fondsInitial, $observation);

            return to_route('caisses.index')->with('alert', 'Session de caisse #'.$caisse->id.' ouverte avec succès.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['user_id' => $e->getMessage()]);
        }
    }

    /**
     * Affiche l'état détaillé d'une caisse et ses mouvements d'espèces.
     */
    public function show(Caisse $caiss)
    {
        $caiss->load(['user', 'mouvements.user']);

        return view('caisses.show', ['caisse' => $caiss]);
    }

    /**
     * Formulaire de modification ou clôture de caisse.
     */
    public function edit(Caisse $caiss)
    {
        $users = User::orderBy('nom')->get();

        $statuts = [
            'ouverte' => 'Ouverte',
            'fermee' => 'Fermée',
        ];

        return view('caisses.edit', ['caisse' => $caiss, 'users' => $users, 'statuts' => $statuts]);
    }

    /**
     * Met à jour ou clôture la session de caisse.
     */
    public function update(CaisseRequest $request, Caisse $caiss)
    {
        $validated = $request->validated();

        if (isset($validated['statut']) && $validated['statut'] === 'fermee' && $caiss->statut !== 'fermee') {
            try {
                $soldePhysique = (float) ($validated['solde_physique'] ?? $caiss->solde_theorique);
                $observation = $validated['observation'] ?? null;

                $this->caisseService->fermerCaisse($caiss, $soldePhysique, $observation);

                return to_route('caisses.index')->with('alert', 'Session de caisse #'.$caiss->id.' fermée avec succès. Écart comptabilisé.');
            } catch (\InvalidArgumentException $e) {
                return back()->withInput()->withErrors(['solde_physique' => $e->getMessage()]);
            }
        }

        $caiss->update($validated);

        return to_route('caisses.index')->with('alert', 'Mise à jour de la caisse effectuée avec succès.');
    }

    /**
     * Supprime une session de caisse.
     */
    public function destroy(Caisse $caiss)
    {
        $caiss->delete();

        return to_route('caisses.index')->with('alert', 'Session de caisse supprimée avec succès.');
    }

    /**
     * Clôture directe de session de caisse avec comptage physique et calcul d'écart.
     */
    public function cloturer(Request $request, Caisse $caiss)
    {
        $request->validate([
            'solde_physique' => 'required|numeric|min:0',
            'observation' => 'nullable|string',
        ]);

        try {
            $soldePhysique = (float) $request->input('solde_physique');
            $observation = $request->input('observation');

            $this->caisseService->fermerCaisse($caiss, $soldePhysique, $observation);

            return to_route('caisses.show', $caiss)->with('alert', 'Session de caisse #'.$caiss->id.' clôturée avec succès. Bilan disponible pour impression.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['solde_physique' => $e->getMessage()]);
        }
    }

    /**
     * Impression du Rapport Z de Clôture Journalière (Ticket thermique 80mm).
     */
    public function print(Caisse $caiss)
    {
        $caiss->load(['user', 'mouvements.user']);

        return view('caisses.print', ['caisse' => $caiss]);
    }
}
