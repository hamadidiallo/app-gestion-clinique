<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personnel\MedecinRequest;
use App\Models\Administration\User;
use App\Models\Facturation\Service;
use App\Models\Personnel\Medecin;
use App\Traits\HasPeriodFilter;
use Illuminate\Support\Collection;

class MedecinController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche la liste complète des médecins enregistrés dans la clinique avec leurs activités filtrées par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $medecins = Medecin::withCount(['prestations' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_prestation');
        }])->withSum(['prestations' => function ($q) use ($filter) {
            $this->applyDateFilter($q, $filter['start'], $filter['end'], 'date_prestation');
        }], 'part_medecin')->orderBy('nom')->get();

        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('personnel.medecins.index', compact('medecins', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau médecin.
     */
    public function create()
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        $comptes = $this->comptesMedecinDisponibles();
        $services = Service::orderBy('nom')->get();

        return view('personnel.medecins.create', compact('statuts', 'comptes', 'services'));
    }

    /**
     * Comptes « Médecin » de la clinique qui ne sont pas encore reliés à une fiche
     * (plus, en modification, celui déjà relié à la fiche en cours).
     */
    private function comptesMedecinDisponibles(?Medecin $medecin = null): Collection
    {
        $comptesDejaRelies = Medecin::whereNotNull('user_id')
            ->when($medecin, fn ($requete) => $requete->whereKeyNot($medecin->id))
            ->pluck('user_id');

        return User::with('role')
            ->where('clinique_id', auth()->user()->clinique_id)
            ->whereNotIn('id', $comptesDejaRelies)
            ->orderBy('nom')
            ->get()
            ->filter(fn (User $compte) => $compte->hasRole('Médecin'))
            ->values();
    }

    /**
     * Enregistre un nouveau médecin dans la base de données après validation.
     */
    public function store(MedecinRequest $request)
    {
        $validated = $request->validated();

        Medecin::create($validated);

        return to_route('medecins.index')->with('alert', 'Médecin enregistré avec succès.');
    }

    /**
     * Affiche les détails spécifiques d'un médecin donné avec statistiques et historique.
     */
    public function show(Medecin $medecin)
    {
        $medecin->load([
            'prestations' => function ($q) {
                $q->with(['patient', 'acte', 'service'])->latest('date_prestation');
            },
            'remunerations' => function ($q) {
                $q->latest('created_at');
            },
            'consultations' => function ($q) {
                $q->with('patient')->latest('date_consultation');
            },
        ]);

        $totalActes = $medecin->prestations->count();
        $totalChiffreAffaires = (float) $medecin->prestations->sum('montant');
        $totalPartMedecin = (float) $medecin->prestations->sum('part_medecin');
        $totalPaye = (float) $medecin->remunerations->where('statut', 'paye')->sum('montant_medecin');
        $soldeDu = max(0, $totalPartMedecin - $totalPaye);

        return view('personnel.medecins.show', compact('medecin', 'totalActes', 'totalChiffreAffaires', 'totalPartMedecin', 'totalPaye', 'soldeDu'));
    }

    /**
     * Affiche le formulaire d'édition des informations d'un médecin existant.
     */
    public function edit(Medecin $medecin)
    {
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        $comptes = $this->comptesMedecinDisponibles($medecin);
        $services = Service::orderBy('nom')->get();

        return view('personnel.medecins.edit', compact('medecin', 'statuts', 'comptes', 'services'));
    }

    /**
     * Met à jour les informations du médecin en base de données.
     */
    public function update(MedecinRequest $request, Medecin $medecin)
    {
        $validated = $request->validated();

        $medecin->update($validated);

        return to_route('medecins.index')->with('alert', 'Fiche du médecin mise à jour avec succès.');
    }

    /**
     * Supprime la fiche d'un médecin de la base de données.
     */
    public function destroy(Medecin $medecin)
    {
        $medecin->delete();

        return to_route('medecins.index')->with('alert', 'Médecin supprimé avec succès.');
    }
}
