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
     * Affiche l'ensemble des sessions de caisses (filtrées par utilisateur pour les caissiers).
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $user = auth()->user();
        $isCaissier = $user && $user->hasRole('Caissier');

        $query = Caisse::with('user')->latest('date_ouverture');

        // Isolation stricte : une caissière ne voit QUE ses propres caisses
        if ($isCaissier) {
            $query->where('user_id', $user->id);
        }

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_ouverture');

        $caisses = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        // Caisse actuellement ouverte pour cet utilisateur
        $activeCaisse = $isCaissier
            ? Caisse::where('user_id', $user->id)->where('statut', 'ouverte')->latest('date_ouverture')->first()
            : null;

        return view('caisses.index', compact('caisses', 'currentPeriod', 'periodLabel', 'activeCaisse', 'isCaissier'));
    }

    /**
     * Formulaire d'ouverture d'une nouvelle session de caisse.
     */
    public function create()
    {
        $user = auth()->user();
        $isCaissier = $user && $user->hasRole('Caissier');

        // Un caissier ne peut ouvrir de caisse que pour lui-même
        $users = $isCaissier ? User::where('id', $user->id)->get() : User::orderBy('nom')->get();

        $statuts = [
            'ouverte' => 'Ouverte',
            'fermee' => 'Fermée',
        ];

        return view('caisses.create', compact('users', 'statuts', 'isCaissier'));
    }

    /**
     * Ouvre et enregistre une session de caisse via GestionCaisseService.
     */
    public function store(CaisseRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $isCaissier = $user && $user->hasRole('Caissier');

        try {
            // Pour un caissier, forcer l'attribution à son propre compte
            $userId = $isCaissier ? $user->id : (int) ($validated['user_id'] ?? $user->id);
            $fondsInitial = (float) ($validated['fonds_initial'] ?? 0);
            $observation = $validated['observation'] ?? null;

            $caisse = $this->caisseService->ouvrirCaisse($userId, $fondsInitial, $observation);

            return to_route('caisses.show', $caisse)->with('alert', 'Session de caisse #'.$caisse->id.' ouverte avec succès. Votre guichet est prêt.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['user_id' => $e->getMessage()]);
        }
    }

    /**
     * Affiche l'état détaillé d'une caisse et ses mouvements d'espèces.
     */
    public function show(Caisse $caiss)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('Caissier') && $caiss->user_id !== $user->id) {
            abort(403, 'Accès interdit : vous ne pouvez consulter que vos propres sessions de caisse.');
        }

        $caiss->load(['user', 'mouvements.user']);

        return view('caisses.show', ['caisse' => $caiss]);
    }

    /**
     * Formulaire de modification ou clôture de caisse.
     */
    public function edit(Caisse $caiss)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('Caissier') && $caiss->user_id !== $user->id) {
            abort(403, 'Accès interdit : vous ne pouvez modifier que vos propres sessions de caisse.');
        }

        $isCaissier = $user && $user->hasRole('Caissier');
        $users = $isCaissier ? User::where('id', $user->id)->get() : User::orderBy('nom')->get();

        $statuts = [
            'ouverte' => 'Ouverte',
            'fermee' => 'Fermée',
        ];

        return view('caisses.edit', ['caisse' => $caiss, 'users' => $users, 'statuts' => $statuts, 'isCaissier' => $isCaissier]);
    }

    /**
     * Met à jour ou clôture la session de caisse.
     */
    public function update(CaisseRequest $request, Caisse $caiss)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('Caissier') && $caiss->user_id !== $user->id) {
            abort(403, 'Accès interdit.');
        }

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
        $user = auth()->user();
        if ($user && $user->hasRole('Caissier')) {
            abort(403, 'Seul un administrateur peut supprimer une session de caisse.');
        }

        $caiss->delete();

        return to_route('caisses.index')->with('alert', 'Session de caisse supprimée avec succès.');
    }

    /**
     * Clôture directe de session de caisse avec comptage physique et calcul d'écart.
     */
    public function cloturer(Request $request, Caisse $caiss)
    {
        $user = auth()->user();
        if ($user && $user->hasRole('Caissier') && $caiss->user_id !== $user->id) {
            abort(403, 'Accès interdit.');
        }

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
        $user = auth()->user();
        if ($user && $user->hasRole('Caissier') && $caiss->user_id !== $user->id) {
            abort(403, 'Accès interdit.');
        }

        $caiss->load(['user', 'mouvements.user']);

        return view('caisses.print', ['caisse' => $caiss]);
    }
}
