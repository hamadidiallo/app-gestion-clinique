<?php

namespace App\Services;

use App\Models\Caisse;
use App\Models\Depense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepenseService
{
    public function __construct(
        protected GestionCaisseService $caisseService,
        protected JournalActiviteService $journalService
    ) {}

    /**
     * Enregistre une dépense validée et crée automatiquement le mouvement de caisse correspondant.
     *
     * @param array $donnees
     * @return Depense
     */
    public function enregistrerDepense(array $donnees): Depense
    {
        return DB::transaction(function () use ($donnees) {
            $effectiveUserId = $donnees['user_id'] ?? auth()->id();

            if (!$effectiveUserId) {
                $user = User::first();
                $effectiveUserId = $user ? $user->id : null;
            }

            if (!$effectiveUserId) {
                throw new \InvalidArgumentException("Identifiant utilisateur requis pour enregistrer une dépense.");
            }

            if (empty($donnees['reference'])) {
                $donnees['reference'] = 'DEP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }

            $donnees['user_id'] = $effectiveUserId;
            $donnees['date_depense'] = $donnees['date_depense'] ?? Carbon::now()->toDateString();
            $donnees['statut'] = $donnees['statut'] ?? true;

            $depense = Depense::create($donnees);

            // Mouvement de caisse automatique si la dépense est validée (statut = true)
            if ($depense->statut) {
                $caisseOuverte = Caisse::where('user_id', $effectiveUserId)
                    ->where('statut', 'ouverte')
                    ->first() ?? Caisse::where('statut', 'ouverte')->first();

                if ($caisseOuverte) {
                    $this->caisseService->enregistrerMouvement(
                        caisse: $caisseOuverte,
                        userId: $effectiveUserId,
                        type: 'sortie',
                        origine: 'depense',
                        reference: $depense->reference,
                        montant: (float) $depense->montant,
                        description: 'Sortie caisse dépense #' . $depense->reference . ' (' . ($depense->beneficiaire ?? 'Sans bénéficiaire') . ')'
                    );
                }
            }

            $this->journalService->log(
                action: 'creation_depense',
                module: 'depense',
                objetType: Depense::class,
                objetId: $depense->id,
                description: 'Enregistrement dépense de ' . $depense->montant . ' FCFA (Réf: ' . $depense->reference . ')',
                nouvellesValeurs: $depense->toArray()
            );

            return $depense;
        });
    }
}
