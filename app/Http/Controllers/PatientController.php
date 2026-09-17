<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Assurance;
use App\Models\CarteAssurance;
use App\Models\DossierMedical;
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
            'cartesAssurance.reference',
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
            $rawQuery = implode(' ', array_filter($rawQuery, fn ($i) => is_string($i) || is_numeric($i)));
        }
        $query = trim((string) $rawQuery);

        if (empty($query)) {
            return response()->json([]);
        }

        $patients = Patient::with(['assurance', 'cartesAssurance' => function ($q) {
            $q->where('statut', true)->with('assurance');
        }])
            ->where(function ($q) use ($query) {
                $q->where('prenom', 'LIKE', "%{$query}%")
                    ->orWhere('nom', 'LIKE', "%{$query}%")
                    ->orWhere('telephone', 'LIKE', "%{$query}%")
                    ->orWhere('numero_assure', 'LIKE', "%{$query}%")
                    ->orWhereHas('cartesAssurance', function ($cq) use ($query) {
                        $cq->where('reference', 'LIKE', "%{$query}%");
                    });
            })
            ->limit(10)
            ->get();

        $formatted = $patients->map(function ($patient) {
            $carte = $patient->cartesAssurance->first();
            $assuranceId = $patient->assurance_id ?? ($carte ? $carte->assurance_id : null);
            $assuranceNom = $patient->assurance ? $patient->assurance->nom : ($carte && $carte->assurance ? $carte->assurance->nom : null);
            $numeroAssure = $patient->numero_assure ?? ($carte ? $carte->reference : null);
            $tauxCouverture = $patient->taux_couverture ?? ($carte ? (float) $carte->taux_couverture : ($patient->assurance ? (float) $patient->assurance->taux_par_defaut : 0));

            return [
                'id' => $patient->id,
                'nom_complet' => $patient->prenom.' '.$patient->nom,
                'telephone' => $patient->telephone ?? 'Sans téléphone',
                'statut' => $patient->statut,
                'assurance_id' => $assuranceId,
                'assurance_nom' => $assuranceNom,
                'numero_assure' => $numeroAssure,
                'carte_reference' => $numeroAssure,
                'taux_couverture' => (float) $tauxCouverture,
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

        $numeroAssure = $validated['numero_assure'] ?? $validated['carte_reference'] ?? null;
        $assuranceId = $validated['statut'] === 'assure' ? ($validated['assurance_id'] ?? null) : null;
        $tauxCouverture = $validated['statut'] === 'assure' ? ($validated['taux_couverture'] ?? null) : null;

        $patient = Patient::create([
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'sexe' => $validated['sexe'],
            'telephone' => $validated['telephone'] ?? null,
            'statut' => $validated['statut'],
            'assurance_id' => $assuranceId,
            'numero_assure' => $numeroAssure,
            'taux_couverture' => $tauxCouverture,
        ]);

        if ($assuranceId) {
            CarteAssurance::updateOrCreate(
                ['patient_id' => $patient->id],
                [
                    'assurance_id' => $assuranceId,
                    'reference' => $numeroAssure ?: ('CARD-'.strtoupper(Str::random(6))),
                    'taux_couverture' => $tauxCouverture ?? 80,
                    'statut' => true,
                ]
            );
        }

        // Enregistrement initial du dossier médical si renseigné
        if (! empty($validated['groupe_sanguin']) || ! empty($validated['allergies']) || ! empty($validated['antecedents_personnels'])) {
            DossierMedical::updateOrCreate(
                ['patient_id' => $patient->id],
                [
                    'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
                    'allergies' => $validated['allergies'] ?? null,
                    'antecedents_personnels' => $validated['antecedents_personnels'] ?? null,
                ]
            );
        }

        if ($request->input('action') === 'save_and_ticket') {
            return to_route('tickets.create', ['patient_id' => $patient->id])
                ->with('alert', 'Patient '.$patient->prenom.' '.$patient->nom.' créé avec succès. Vous pouvez maintenant émettre son ticket.');
        }

        return to_route('patients.show', $patient)->with('alert', 'Dossier du patient '.$patient->prenom.' '.$patient->nom.' créé avec succès.');
    }

    /**
     * Affiche les détails d'un patient spécifique et son dossier médical complet.
     */
    public function show(Patient $patient)
    {
        $patient->load([
            'assurance',
            'cartesAssurance.assurance',
            'tickets' => function ($q) {
                $q->latest('date_ticket')->with(['details.prestation', 'service', 'medecin', 'paiements.modePaiement', 'dette']);
            },
            'prestations' => function ($q) {
                $q->latest('date_prestation')->with(['service', 'medecin', 'tarif']);
            },
            'dossierMedical',
            'consultations' => function ($q) {
                $q->latest('date_consultation')->with(['medecin', 'ordonnance.lignes', 'ticket']);
            },
            'dettes' => function ($q) {
                $q->latest('date_creation')->with('ticket');
            },
        ]);

        return view('patients.show', compact('patient'));
    }

    /**
     * Met à jour ou initialise le dossier médical permanent du patient.
     */
    public function updateDossierMedical(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'groupe_sanguin' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'antecedents_personnels' => 'nullable|string',
            'antecedents_familiaux' => 'nullable|string',
            'antecedents_chirurgicaux' => 'nullable|string',
            'notes_particulieres' => 'nullable|string',
        ]);

        $dossier = DossierMedical::firstOrCreate(
            ['patient_id' => $patient->id],
            ['numero_dossier' => 'DM-'.date('Y').'-'.str_pad((string) $patient->id, 5, '0', STR_PAD_LEFT)]
        );

        $dossier->update($validated);

        return back()->with('alert', 'Dossier médical permanent mis à jour avec succès.');
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

        $patient->load(['assurance', 'cartesAssurance' => function ($q) {
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

        $numeroAssure = $validated['numero_assure'] ?? $validated['carte_reference'] ?? null;
        $assuranceId = $validated['statut'] === 'assure' ? ($validated['assurance_id'] ?? null) : null;
        $tauxCouverture = $validated['statut'] === 'assure' ? ($validated['taux_couverture'] ?? null) : null;

        $patient->update([
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'sexe' => $validated['sexe'],
            'telephone' => $validated['telephone'] ?? null,
            'statut' => $validated['statut'],
            'assurance_id' => $assuranceId,
            'numero_assure' => $numeroAssure,
            'taux_couverture' => $tauxCouverture,
        ]);

        if ($assuranceId) {
            CarteAssurance::updateOrCreate(
                ['patient_id' => $patient->id],
                [
                    'assurance_id' => $assuranceId,
                    'reference' => $numeroAssure ?: ('CARD-'.strtoupper(Str::random(6))),
                    'taux_couverture' => $tauxCouverture ?? 80,
                    'statut' => true,
                ]
            );
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
