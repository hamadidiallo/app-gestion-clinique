<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Assurance;
use App\Models\CarteAssurance;
use App\Models\Patient;
use App\Traits\HasPeriodFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche la liste de tous les patients enregistrés filtrés par période d'admission.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Patient::with(['cartesAssurance' => function ($q) {
            $q->where('statut', true)->with('assurance');
        }])->latest();

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'created_at');

        // Filtrage de recherche par ID, Nom, Prénom, Téléphone et N° Carte d'Assurance
        $this->applySearchFilter($query, request('q', request('search')), [
            'id',
            'nom',
            'prenom',
            'telephone',
            'cartesAssurance.reference'
        ]);

        if (request('statut')) {
            $statutVal = request('statut');
            if (in_array($statutVal, ['sexe_M', 'M', 'homme'])) {
                $query->where('sexe', 'M');
            } elseif (in_array($statutVal, ['sexe_F', 'F', 'femme'])) {
                $query->where('sexe', 'F');
            } else {
                $query->where('statut', $statutVal);
            }
        }

        if (request('sexe')) {
            $query->where('sexe', request('sexe'));
        }

        $patients = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        $statuses = [
            'assure' => 'Statut : Assuré',
            'non_assure' => 'Statut : Non Assuré (Privé)',
            'sexe_M' => 'Sexe : Homme (M)',
            'sexe_F' => 'Sexe : Femme (F)',
        ];

        return view('patients.index', compact('patients', 'currentPeriod', 'periodLabel', 'statuses'));
    }

    /**
     * Recherche les patients par leur nom ou prénom pour l'autocomplétion (format JSON).
     */
    public function search(Request $request)
    {
        $rawQuery = $request->input('q', '');
        if (is_array($rawQuery)) {
            $rawQuery = implode(' ', array_filter($rawQuery, fn($i) => is_string($i) || is_numeric($i)));
        }
        $query = trim((string) $rawQuery);

        if (empty($query)) {
            return response()->json([]);
        }

        $patients = Patient::with(['cartesAssurance' => function ($q) {
            $q->where('statut', true)->with('assurance');
        }])
            ->where('prenom', 'LIKE', "%{$query}%")
            ->orWhere('nom', 'LIKE', "%{$query}%")
            ->orWhere('telephone', 'LIKE', "%{$query}%")
            ->orWhereHas('cartesAssurance', function ($q) use ($query) {
                $q->where('reference', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

        $formatted = $patients->map(function ($patient) {
            $carte = $patient->cartesAssurance->first();
            return [
                'id' => $patient->id,
                'nom_complet' => $patient->prenom . ' ' . $patient->nom,
                'telephone' => $patient->telephone ?? 'Sans téléphone',
                'statut' => $patient->statut,
                'assurance_id' => $carte ? $carte->assurance_id : null,
                'assurance_nom' => $carte && $carte->assurance ? $carte->assurance->nom : null,
                'carte_reference' => $carte ? $carte->reference : null,
                'taux_couverture' => $carte ? (float) $carte->taux_couverture : 0,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Affiche le formulaire de création d'un patient.
     */
    public function create()
    {
        $sexes = [
            'M' => 'Masculin',
            'F' => 'Féminin',
        ];

        $statuts = [
            'assure' => 'Assuré',
            'non_assure' => 'Non Assuré',
        ];

        $assurances = Assurance::where('statut', true)->orderBy('nom')->get();

        return view('patients.create', compact('sexes', 'statuts', 'assurances'));
    }

    /**
     * Enregistre un nouveau patient dans la base de données avec sa carte d'assurance si disponible.
     */
    public function store(PatientRequest $request)
    {
        $validated = $request->validated();

        $patient = Patient::create([
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'sexe' => $validated['sexe'],
            'telephone' => $validated['telephone'] ?? null,
            'statut' => $validated['statut'],
        ]);

        if ($validated['statut'] === 'assure' && !empty($validated['assurance_id'])) {
            $reference = !empty($validated['carte_reference']) 
                ? $validated['carte_reference'] 
                : 'CARD-' . strtoupper(Str::random(6));

            CarteAssurance::create([
                'patient_id' => $patient->id,
                'assurance_id' => $validated['assurance_id'],
                'reference' => $reference,
                'taux_couverture' => $validated['taux_couverture'] ?? 80,
                'statut' => true,
            ]);
        }

        return to_route('patients.index')->with('alert', 'Création du patient réussie');
    }

    /**
     * Affiche les détails d'un patient spécifique.
     */
    public function show(Patient $patient)
    {
        $patient->load(['cartesAssurance.assurance', 'tickets', 'prestations']);

        return view('patients.show', compact('patient'));
    }

    /**
     * Affiche le formulaire d'édition d'un patient existant.
     */
    public function edit(Patient $patient)
    {
        $sexes = [
            'M' => 'Masculin',
            'F' => 'Féminin',
        ];

        $statuts = [
            'assure' => 'Assuré',
            'non_assure' => 'Non Assuré',
        ];

        $assurances = Assurance::where('statut', true)->orderBy('nom')->get();

        $patient->load(['cartesAssurance' => function ($q) {
            $q->where('statut', true);
        }]);
        $carteAssurance = $patient->cartesAssurance->first();

        return view('patients.edit', compact('patient', 'sexes', 'statuts', 'assurances', 'carteAssurance'));
    }

    /**
     * Met à jour les informations d'un patient et de sa carte d'assurance.
     */
    public function update(PatientRequest $request, Patient $patient)
    {
        $validated = $request->validated();

        $patient->update([
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'sexe' => $validated['sexe'],
            'telephone' => $validated['telephone'] ?? null,
            'statut' => $validated['statut'],
        ]);

        if ($validated['statut'] === 'assure' && !empty($validated['assurance_id'])) {
            $carte = CarteAssurance::where('patient_id', $patient->id)->where('statut', true)->first();

            $reference = !empty($validated['carte_reference']) 
                ? $validated['carte_reference'] 
                : ($carte ? $carte->reference : 'CARD-' . strtoupper(Str::random(6)));

            if ($carte) {
                $carte->update([
                    'assurance_id' => $validated['assurance_id'],
                    'reference' => $reference,
                    'taux_couverture' => $validated['taux_couverture'] ?? $carte->taux_couverture ?? 80,
                    'statut' => true,
                ]);
            } else {
                CarteAssurance::create([
                    'patient_id' => $patient->id,
                    'assurance_id' => $validated['assurance_id'],
                    'reference' => $reference,
                    'taux_couverture' => $validated['taux_couverture'] ?? 80,
                    'statut' => true,
                ]);
            }
        } elseif ($validated['statut'] === 'non_assure') {
            CarteAssurance::where('patient_id', $patient->id)->update(['statut' => false]);
        }

        return to_route('patients.index')->with('alert', 'Modification du patient réussie');
    }

    /**
     * Supprime un patient de la base de données.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return to_route('patients.index')->with('alert', 'Suppression du patient réussie');
    }
}
