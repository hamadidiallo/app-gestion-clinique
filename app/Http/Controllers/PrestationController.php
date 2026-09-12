<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrestationRequest;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Service;
use App\Models\Tarif;
use App\Services\PrestationService;
use App\Services\TicketService;
use App\Traits\HasPeriodFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrestationController extends Controller
{
    use HasPeriodFilter;

    public function __construct(
        protected PrestationService $prestationService,
        protected TicketService $ticketService
    ) {}

    /**
     * Affiche la liste de toutes les prestations enregistrées filtrées par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Prestation::with(['patient', 'service', 'medecin', 'tarif'])->latest('date_prestation');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_prestation');

        $prestations = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('prestations.index', compact('prestations', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle prestation.
     */
    public function create()
    {
        $medecins = Medecin::where('statut', true)->get();
        $patients = Patient::orderBy('nom')->get();
        $services = Service::where('statut', true)->orderBy('nom')->get();
        $tarifs = Tarif::with('service')->where('statut', true)->get();

        $statuts = [
            '1' => 'Effectuée / Active',
            '0' => 'Annulée / Inactive',
        ];

        $selectedPatient = old('patient_id') ? Patient::find(old('patient_id')) : null;
        $selectedService = old('service_id') ? Service::find(old('service_id')) : null;

        return view('prestations.create', compact('medecins', 'patients', 'services', 'tarifs', 'statuts', 'selectedPatient', 'selectedService'));
    }

    /**
     * Endpoint AJAX pour la prévisualisation temps réel des calculs (Tarif, Assurance, Part Médecin, Part Clinique).
     */
    public function previewCalculs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'medecin_id' => 'nullable|exists:medecins,id',
            'date_prestation' => 'nullable|date',
        ]);

        try {
            $calculs = $this->prestationService->calculerPrestationAutomatique(
                (int) $validated['patient_id'],
                (int) $validated['service_id'],
                !empty($validated['medecin_id']) ? (int) $validated['medecin_id'] : null,
                $validated['date_prestation'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'tarif_brut' => $calculs['montant'],
                    'taux_couverture' => $calculs['taux_couverture'],
                    'montant_assurance' => $calculs['montant_assurance'],
                    'montant_patient' => $calculs['montant_patient'],
                    'pourcentage_medecin' => $calculs['pourcentage_medecin'],
                    'pourcentage_clinique' => $calculs['pourcentage_clinique'],
                    'part_medecin' => $calculs['part_medecin'],
                    'part_clinique' => $calculs['part_clinique'],
                    'medecin_nom' => $calculs['medecin'] ? 'Dr ' . $calculs['medecin']->nom . ' ' . $calculs['medecin']->prenom : 'Non affecté',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Enregistre une nouvelle prestation médicale et crée automatiquement le ticket correspondant.
     */
    public function store(PrestationRequest $request)
    {
        $validated = $request->validated();

        try {
            $prestation = $this->prestationService->creerPrestationAutomatique($validated);

            // Générer le ticket directement
            $ticket = $this->ticketService->creerTicketDepuisPrestations([$prestation->id]);

            return to_route('tickets.print', $ticket)->with('alert', 'Prestation et Ticket N° ' . $ticket->reference . ' enregistrés automatiquement avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['service_id' => $e->getMessage()]);
        }
    }

    /**
     * Affiche les détails d'une prestation médicale spécifique.
     */
    public function show(Prestation $prestation)
    {
        $prestation->load(['patient', 'service', 'medecin', 'tarif', 'ticketDetails.ticket']);

        return view('prestations.show', compact('prestation'));
    }

    /**
     * Affiche le formulaire d'édition d'une prestation existante.
     */
    public function edit(Prestation $prestation)
    {
        $prestation->load(['patient', 'service', 'medecin', 'tarif']);

        $medecins = Medecin::get();
        $patients = Patient::orderBy('nom')->get();
        $services = Service::orderBy('nom')->get();
        $tarifs = Tarif::with('service')->get();

        $statuts = [
            '1' => 'Effectuée / Active',
            '0' => 'Annulée / Inactive',
        ];

        $patientId = old('patient_id', $prestation->patient_id);
        $selectedPatient = $patientId ? Patient::find($patientId) : null;

        $serviceId = old('service_id', $prestation->service_id);
        $selectedService = $serviceId ? Service::find($serviceId) : null;

        return view('prestations.edit', compact('prestation', 'medecins', 'patients', 'services', 'tarifs', 'statuts', 'selectedPatient', 'selectedService'));
    }

    /**
     * Met à jour les informations d'une prestation en base de données.
     */
    public function update(PrestationRequest $request, Prestation $prestation)
    {
        $validated = $request->validated();

        $prestation->update($validated);

        return to_route('prestations.index')->with('alert', 'Modification de la prestation réussie');
    }

    /**
     * Supprime une prestation médicale de la base de données.
     */
    public function destroy(Prestation $prestation)
    {
        $prestation->delete();

        return to_route('prestations.index')->with('alert', 'Suppression de la prestation réussie');
    }
}
