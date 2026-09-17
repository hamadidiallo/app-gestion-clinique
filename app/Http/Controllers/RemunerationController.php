<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemunerationRequest;
use App\Models\Medecin;
use App\Models\Prestation;
use App\Models\Remuneration;
use App\Models\User;
use App\Services\RemunerationService;
use App\Traits\HasPeriodFilter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        // Synthèse KPI
        $stats = [
            'total_medecin' => (float) $remunerations->sum('montant_medecin'),
            'total_clinique' => (float) $remunerations->sum('montant_clinique'),
            'total_base' => (float) $remunerations->sum('montant_base'),
            'total_paye' => (float) $remunerations->whereIn('statut', ['payee', 'paye'])->sum('montant_medecin'),
            'total_attente' => (float) $remunerations->whereNotIn('statut', ['payee', 'paye', 'annulee'])->sum('montant_medecin'),
            'nb_medecins' => Medecin::where('statut', true)->count(),
        ];

        return view('remunerations.index', compact('remunerations', 'currentPeriod', 'periodLabel', 'stats'));
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
     * Endpoint AJAX pour prévisualiser les calculs de rétrocession avant validation.
     */
    public function previewCalcul(Request $request): JsonResponse
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',
        ]);

        $medecin = Medecin::findOrFail($request->medecin_id);
        $debut = Carbon::parse($request->periode_debut)->startOfDay();
        $fin = Carbon::parse($request->periode_fin)->endOfDay();

        $type = $medecin->type_remuneration ?? 'pourcentage';
        $salaireFixe = (float) ($medecin->salaire_fixe ?? 0);
        $pourcentage = (float) ($medecin->pourcentage ?? 50);

        $prestations = Prestation::with(['acte', 'patient', 'service'])
            ->where('medecin_id', $medecin->id)
            ->where('statut', true)
            ->whereBetween('date_prestation', [$debut, $fin])
            ->get();

        $nbActes = $prestations->count();
        $montantBase = (float) $prestations->sum('montant');
        $montantMedecin = $type === 'salaire_fixe' ? $salaireFixe : (float) $prestations->sum('part_medecin');
        $montantClinique = $type === 'salaire_fixe' ? 0.0 : (float) $prestations->sum('part_clinique');

        return response()->json([
            'success' => true,
            'data' => [
                'type_remuneration' => $type,
                'salaire_fixe' => $salaireFixe,
                'pourcentage' => $pourcentage,
                'nb_prestations' => $nbActes,
                'montant_base' => $montantBase,
                'montant_medecin' => $montantMedecin,
                'montant_clinique' => $montantClinique,
            ],
        ]);
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

            return to_route('remunerations.show', $remuneration)->with('alert', 'Rémunération calculée automatiquement avec succès ('.number_format($remuneration->montant_medecin, 0, ',', ' ').' FCFA).');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['medecin_id' => $e->getMessage()]);
        }
    }

    /**
     * Détails d'une rémunération médicale avec la liste des actes réalisés.
     */
    public function show(Remuneration $remuneration)
    {
        $remuneration->load(['medecin', 'user']);

        $debut = $remuneration->periode_debut ? Carbon::parse($remuneration->periode_debut)->startOfDay() : now()->startOfMonth();
        $fin = $remuneration->periode_fin ? Carbon::parse($remuneration->periode_fin)->endOfDay() : now()->endOfMonth();

        $prestations = Prestation::with(['patient', 'acte', 'service'])
            ->where('medecin_id', $remuneration->medecin_id)
            ->where('statut', true)
            ->whereBetween('date_prestation', [$debut, $fin])
            ->latest('date_prestation')
            ->get();

        return view('remunerations.show', compact('remuneration', 'prestations'));
    }

    /**
     * Valide le règlement/paiement immédiat d'une rémunération.
     */
    public function validerPaiement(Remuneration $remuneration)
    {
        if ($remuneration->statut === 'payee') {
            return back()->with('alert', 'Cette rémunération est déjà réglée.');
        }

        $this->remunerationService->validerPaiementRemuneration($remuneration);

        return back()->with('alert', 'Paiement de la rémunération validé avec succès.');
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
