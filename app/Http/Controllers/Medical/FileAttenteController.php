<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * File d'attente du médecin connecté.
 *
 * Seuls les tickets payés du jour y figurent. Un patient y reste tant que sa consultation
 * n'a pas été ouverte par le médecin (aucune consultation, ou constantes prises à l'accueil).
 */
class FileAttenteController extends Controller
{
    public function index(): View
    {
        $medecin = auth()->user()->medecin;

        if (! $medecin) {
            return view('medical.file-attente.index', ['medecin' => null]);
        }

        $enAttente = $this->ticketsEnAttente()
            ->where('medecin_id', $medecin->id)
            ->get();

        $aPrendre = $medecin->service_id
            ? $this->ticketsEnAttente()->whereNull('medecin_id')->where('service_id', $medecin->service_id)->get()
            : collect();

        $enConsultation = Consultation::with('patient')
            ->where('medecin_id', $medecin->id)
            ->where('statut', 'en_cours')
            ->orderBy('date_consultation')
            ->get();

        $vusAujourdhui = Consultation::with('patient')
            ->where('medecin_id', $medecin->id)
            ->where('statut', 'terminee')
            ->whereDate('date_consultation', today())
            ->latest('date_consultation')
            ->get();

        return view('medical.file-attente.index', compact('medecin', 'enAttente', 'aPrendre', 'enConsultation', 'vusAujourdhui'));
    }

    /**
     * Le médecin prend le patient en charge : le ticket lui est attribué et la consultation s'ouvre.
     * Le verrou évite que deux médecins prennent en même temps le même patient non attribué.
     */
    public function prendre(Ticket $ticket): RedirectResponse
    {
        $medecin = auth()->user()->medecin;
        abort_unless($medecin, 403, 'Votre compte n\'est relié à aucune fiche médecin.');

        [$consultation, $refus] = DB::transaction(function () use ($ticket, $medecin) {
            $ticket = Ticket::with('consultation')->whereKey($ticket->id)->lockForUpdate()->firstOrFail();

            if ($refus = $this->motifDeRefus($ticket, $medecin)) {
                return [null, $refus];
            }

            $ticket->update(['medecin_id' => $medecin->id]);
            $consultation = $ticket->consultation;

            if ($consultation) {
                // Constantes déjà prises à l'accueil : la consultation existe, le médecin la reprend
                $consultation->update(['medecin_id' => $medecin->id, 'statut' => 'en_cours']);
            } else {
                $ticket->load(['service', 'details']);
                $consultation = Consultation::create([
                    'patient_id' => $ticket->patient_id,
                    'medecin_id' => $medecin->id,
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                    'reference' => Consultation::genererReference(),
                    'date_consultation' => now(),
                    'motif_consultation' => $ticket->details->first()?->libelle ?? ($ticket->service?->nom ?? 'Consultation'),
                    'statut' => 'en_cours',
                ]);
            }

            return [$consultation, null];
        });

        if ($refus) {
            return redirect()->route('medecin.file-attente')->with('error', $refus);
        }

        return redirect()->route('consultations.edit', $consultation);
    }

    /**
     * Tickets payés du jour dont la consultation n'a pas encore été ouverte par un médecin.
     */
    private function ticketsEnAttente(): Builder
    {
        return Ticket::with(['patient', 'service', 'consultation'])
            ->where('statut', 'paye')
            ->whereDate('date_ticket', today())
            ->where(fn (Builder $requete) => $requete
                ->whereDoesntHave('consultation')
                ->orWhereHas('consultation', fn (Builder $consultation) => $consultation->where('statut', 'en_attente')))
            ->orderBy('date_ticket');
    }

    private function motifDeRefus(Ticket $ticket, Medecin $medecin): ?string
    {
        if ($ticket->statut !== 'paye') {
            return 'Ce patient n\'est pas encore passé à la caisse.';
        }

        // Comparaisons en entiers : selon le pilote de base, les clés arrivent en texte ou en nombre
        if ($ticket->medecin_id && (int) $ticket->medecin_id !== (int) $medecin->id) {
            return 'Ce patient est déjà pris en charge par un autre médecin.';
        }

        if (! $ticket->medecin_id && (! $medecin->service_id || (int) $ticket->service_id !== (int) $medecin->service_id)) {
            return 'Ce patient relève d\'un autre service.';
        }

        if ($ticket->consultation && ! in_array($ticket->consultation->statut, ['en_attente', 'en_cours'], true)) {
            return 'La consultation de ce patient est déjà terminée.';
        }

        return null;
    }
}
