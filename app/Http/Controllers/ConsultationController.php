<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationRequest;
use App\Models\Acte;
use App\Models\Consultation;
use App\Models\DossierMedical;
use App\Models\Medecin;
use App\Models\Ordonnance;
use App\Models\OrdonnanceLigne;
use App\Models\Patient;
use App\Models\Prestation;
use App\Models\Service;
use App\Models\Ticket;
use App\Services\PrestationService;
use App\Traits\HasPeriodFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche la liste de toutes les consultations médicales avec filtres de date et recherche.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = Consultation::with(['patient', 'medecin', 'ticket', 'ordonnance'])->latest('date_consultation');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'date_consultation');
        $this->applySearchFilter($query, request('q', request('search')), [
            'reference',
            'motif_consultation',
            'diagnostic',
            'patient.nom',
            'patient.prenom',
            'medecin.nom',
            'medecin.prenom',
        ]);

        if (request('statut')) {
            $query->where('statut', request('statut'));
        }

        if (request('medecin_id')) {
            $query->where('medecin_id', request('medecin_id'));
        }

        $consultations = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();

        return view('consultations.index', compact('consultations', 'currentPeriod', 'periodLabel', 'medecins'));
    }

    /**
     * Affiche le formulaire de création d'une consultation (avec ou sans ticket associé).
     */
    public function create(Request $request)
    {
        $ticketId = $request->query('ticket_id');
        $patientId = $request->query('patient_id');

        $ticket = null;
        $selectedPatient = null;
        $defaultMedecinId = null;

        if ($ticketId) {
            $ticket = Ticket::with(['patient.dossierMedical', 'medecin'])->find($ticketId);
            if ($ticket) {
                $selectedPatient = $ticket->patient;
                $defaultMedecinId = $ticket->medecin_id;
            }
        } elseif ($patientId) {
            $selectedPatient = Patient::with('dossierMedical')->find($patientId);
        }

        $patients = Patient::orderBy('nom')->get();
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();

        return view('consultations.create', compact('patients', 'medecins', 'ticket', 'selectedPatient', 'defaultMedecinId'));
    }

    /**
     * Enregistre une consultation médicale, met à jour le dossier médical et génère l'ordonnance si prescrite.
     */
    public function store(ConsultationRequest $request)
    {
        $validated = $request->validated();

        // 1. Récupération ou initialisation du dossier médical du patient
        $patient = Patient::findOrFail($validated['patient_id']);
        $dossier = DossierMedical::firstOrCreate(
            ['patient_id' => $patient->id],
            ['numero_dossier' => 'DOS-'.str_pad($patient->id, 5, '0', STR_PAD_LEFT)]
        );

        // Mise à jour des antécédents si renseignés
        $dossierUpdate = array_filter([
            'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'antecedents_personnels' => $validated['antecedents_personnels'] ?? null,
            'antecedents_familiaux' => $validated['antecedents_familiaux'] ?? null,
        ]);
        if (! empty($dossierUpdate)) {
            $dossier->update($dossierUpdate);
        }

        // 2. Génération de la référence unique de consultation
        $datePrefix = Carbon::now()->format('Ymd');
        $reference = 'CS-'.$datePrefix.'-'.strtoupper(Str::random(4));
        while (Consultation::where('reference', $reference)->exists()) {
            $reference = 'CS-'.$datePrefix.'-'.strtoupper(Str::random(4));
        }

        // 3. Création de la consultation
        $consultation = Consultation::create([
            'patient_id' => $patient->id,
            'medecin_id' => $validated['medecin_id'] ?? null,
            'ticket_id' => $validated['ticket_id'] ?? null,
            'user_id' => auth()->id(),
            'reference' => $reference,
            'date_consultation' => $validated['date_consultation'],
            'tension_arterielle' => $validated['tension_arterielle'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'poids' => $validated['poids'] ?? null,
            'taille' => $validated['taille'] ?? null,
            'pouls' => $validated['pouls'] ?? null,
            'frequence_respiratoire' => $validated['frequence_respiratoire'] ?? null,
            'glycemie' => $validated['glycemie'] ?? null,
            'saturation_oxygene' => $validated['saturation_oxygene'] ?? null,
            'motif_consultation' => $validated['motif_consultation'],
            'histoire_maladie' => $validated['histoire_maladie'] ?? null,
            'examen_physique' => $validated['examen_physique'] ?? null,
            'diagnostic' => $validated['diagnostic'] ?? null,
            'conduite_a_tenir' => $validated['conduite_a_tenir'] ?? null,
            'statut' => $validated['statut'] ?? 'terminee',
        ]);

        // Création automatique du soin (prestation) si consultation directe sans ticket
        if (empty($validated['ticket_id']) && ! empty($validated['medecin_id'])) {
            $acte = Acte::where('categorie', 'consultation')->first()
                ?? Acte::first();
            $service = $acte?->service ?? Service::first();
            $serviceId = $service?->id;

            if ($serviceId) {
                $medecin = Medecin::find($validated['medecin_id']);
                $datePrestation = $consultation->date_consultation ?? Carbon::now();
                $montant = $acte ? (float) $acte->tarif_normal : 5000.0;

                $partage = app(PrestationService::class)->obtenirPourcentagesPartage(
                    $serviceId,
                    $medecin,
                    $datePrestation,
                    $acte
                );

                $partMedecin = round(($montant * $partage['pourcentage_medecin']) / 100.0, 2);
                $partClinique = max(0.0, round($montant - $partMedecin, 2));

                Prestation::create([
                    'patient_id' => $consultation->patient_id,
                    'service_id' => $serviceId,
                    'medecin_id' => $consultation->medecin_id,
                    'acte_id' => $acte?->id,
                    'type' => 'Consultation Médicale',
                    'montant' => $montant,
                    'taux_couverture' => 0,
                    'montant_assurance' => 0,
                    'montant_patient' => $montant,
                    'pourcentage_medecin' => $partage['pourcentage_medecin'],
                    'pourcentage_clinique' => $partage['pourcentage_clinique'],
                    'part_medecin' => $partMedecin,
                    'part_clinique' => $partClinique,
                    'date_prestation' => $datePrestation,
                    'description' => 'Soin généré depuis Consultation '.$consultation->reference,
                    'statut' => true,
                ]);
            }
        }

        // 4. Création de l'ordonnance si des médicaments sont prescrits
        $prescriptions = array_filter($validated['prescriptions'] ?? [], function ($item) {
            return ! empty($item['medicament']);
        });

        if (! empty($prescriptions)) {
            $refOrd = 'ORD-'.$datePrefix.'-'.strtoupper(Str::random(4));
            while (Ordonnance::where('reference', $refOrd)->exists()) {
                $refOrd = 'ORD-'.$datePrefix.'-'.strtoupper(Str::random(4));
            }

            $ordonnance = Ordonnance::create([
                'consultation_id' => $consultation->id,
                'patient_id' => $patient->id,
                'medecin_id' => $validated['medecin_id'] ?? null,
                'reference' => $refOrd,
                'date_ordonnance' => Carbon::parse($validated['date_consultation'])->toDateString(),
                'instructions_generales' => $validated['instructions_generales'] ?? null,
            ]);

            foreach ($prescriptions as $item) {
                OrdonnanceLigne::create([
                    'ordonnance_id' => $ordonnance->id,
                    'medicament' => $item['medicament'],
                    'forme' => $item['forme'] ?? null,
                    'dosage' => $item['dosage'] ?? null,
                    'posologie' => $item['posologie'],
                    'duree' => $item['duree'] ?? null,
                    'instructions' => $item['instructions'] ?? null,
                ]);
            }
        }

        return redirect()->route('consultations.show', $consultation)->with('alert', 'Consultation médicale enregistrée avec succès.');
    }

    /**
     * Affiche les détails complets de la consultation (constantes, examen, ordonnance).
     */
    public function show(Consultation $consultation)
    {
        $consultation->load(['patient.dossierMedical', 'medecin', 'ticket', 'user', 'ordonnance.lignes']);

        return view('consultations.show', compact('consultation'));
    }

    /**
     * Formulaire d'édition de la consultation médicale.
     */
    public function edit(Consultation $consultation)
    {
        $consultation->load(['patient.dossierMedical', 'medecin', 'ordonnance.lignes']);
        $medecins = Medecin::where('statut', true)->orderBy('nom')->get();

        return view('consultations.edit', compact('consultation', 'medecins'));
    }

    /**
     * Met à jour une consultation médicale existante.
     */
    public function update(ConsultationRequest $request, Consultation $consultation)
    {
        $validated = $request->validated();

        $consultation->update([
            'medecin_id' => $validated['medecin_id'] ?? null,
            'date_consultation' => $validated['date_consultation'],
            'tension_arterielle' => $validated['tension_arterielle'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'poids' => $validated['poids'] ?? null,
            'taille' => $validated['taille'] ?? null,
            'pouls' => $validated['pouls'] ?? null,
            'frequence_respiratoire' => $validated['frequence_respiratoire'] ?? null,
            'glycemie' => $validated['glycemie'] ?? null,
            'saturation_oxygene' => $validated['saturation_oxygene'] ?? null,
            'motif_consultation' => $validated['motif_consultation'],
            'histoire_maladie' => $validated['histoire_maladie'] ?? null,
            'examen_physique' => $validated['examen_physique'] ?? null,
            'diagnostic' => $validated['diagnostic'] ?? null,
            'conduite_a_tenir' => $validated['conduite_a_tenir'] ?? null,
            'statut' => $validated['statut'] ?? 'terminee',
        ]);

        // Mise à jour du dossier médical
        if ($consultation->patient && $consultation->patient->dossierMedical) {
            $dossierUpdate = array_filter([
                'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'antecedents_personnels' => $validated['antecedents_personnels'] ?? null,
                'antecedents_familiaux' => $validated['antecedents_familiaux'] ?? null,
            ]);
            if (! empty($dossierUpdate)) {
                $consultation->patient->dossierMedical->update($dossierUpdate);
            }
        }

        // Mise à jour ordonnance
        $prescriptions = array_filter($validated['prescriptions'] ?? [], function ($item) {
            return ! empty($item['medicament']);
        });

        if (! empty($prescriptions)) {
            $ordonnance = $consultation->ordonnance;
            if (! $ordonnance) {
                $datePrefix = Carbon::now()->format('Ymd');
                $refOrd = 'ORD-'.$datePrefix.'-'.strtoupper(Str::random(4));
                $ordonnance = Ordonnance::create([
                    'consultation_id' => $consultation->id,
                    'patient_id' => $consultation->patient_id,
                    'medecin_id' => $validated['medecin_id'] ?? null,
                    'reference' => $refOrd,
                    'date_ordonnance' => Carbon::parse($validated['date_consultation'])->toDateString(),
                    'instructions_generales' => $validated['instructions_generales'] ?? null,
                ]);
            } else {
                $ordonnance->update([
                    'medecin_id' => $validated['medecin_id'] ?? null,
                    'instructions_generales' => $validated['instructions_generales'] ?? null,
                ]);
                $ordonnance->lignes()->delete();
            }

            foreach ($prescriptions as $item) {
                OrdonnanceLigne::create([
                    'ordonnance_id' => $ordonnance->id,
                    'medicament' => $item['medicament'],
                    'forme' => $item['forme'] ?? null,
                    'dosage' => $item['dosage'] ?? null,
                    'posologie' => $item['posologie'],
                    'duree' => $item['duree'] ?? null,
                    'instructions' => $item['instructions'] ?? null,
                ]);
            }
        } elseif ($consultation->ordonnance) {
            $consultation->ordonnance->delete();
        }

        return redirect()->route('consultations.show', $consultation)->with('alert', 'Consultation mise à jour avec succès.');
    }

    /**
     * Supprime une consultation médicale.
     */
    public function destroy(Consultation $consultation)
    {
        $consultation->delete();

        return redirect()->route('consultations.index')->with('alert', 'Consultation supprimée.');
    }

    /**
     * Vue imprimable officielle de l'ordonnance médicale.
     */
    public function printOrdonnance(Consultation $consultation)
    {
        $consultation->load(['patient.dossierMedical', 'medecin', 'ordonnance.lignes']);

        if (! $consultation->ordonnance) {
            return back()->with('error', 'Aucune ordonnance associée à cette consultation.');
        }

        return view('consultations.ordonnance_print', compact('consultation'));
    }
}
