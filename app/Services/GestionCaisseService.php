<?php

namespace App\Services;

use App\Models\Caisse;
use App\Models\MouvementCaisse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GestionCaisseService
{
    public function __construct(
        protected JournalActiviteService $journalService
    ) {}

    /**
     * Calcule le solde théorique de caisse en temps réel depuis les mouvements enregistrés.
     */
    public function calculerSoldeTheorique(Caisse $caisse): float
    {
        $fondsInitial = (float) $caisse->fonds_initial;

        $totalEntrees = (float) MouvementCaisse::where('caisse_id', $caisse->id)
            ->where('type', 'entree')
            ->where('statut', true)
            ->sum('montant');

        $totalSorties = (float) MouvementCaisse::where('caisse_id', $caisse->id)
            ->where('type', 'sortie')
            ->where('statut', true)
            ->sum('montant');

        return round($fondsInitial + $totalEntrees - $totalSorties, 2);
    }

    /**
     * Calcule l'écart de caisse lors du comptage physique.
     * Formula: Écart = Solde Physique - Solde Théorique
     *
     * @param  float  $soldePhysique  Montant d'espèces réellement compté
     * @param  float  $soldeTheorique  Solde théorique calculé par le système
     * @return float Écart (négatif = manquant, positif = excédent)
     */
    public function calculerEcart(float $soldePhysique, float $soldeTheorique): float
    {
        return round($soldePhysique - $soldeTheorique, 2);
    }

    /**
     * Ouvre une nouvelle session de caisse pour un caissier.
     */
    public function ouvrirCaisse(int $userId, float $fondsInitial = 0.0, ?string $observation = null): Caisse
    {
        return DB::transaction(function () use ($userId, $fondsInitial, $observation) {
            $caisseExistante = Caisse::where('user_id', $userId)
                ->where('statut', 'ouverte')
                ->first();

            if ($caisseExistante) {
                throw new \InvalidArgumentException('Une session de caisse est déjà ouverte pour cet utilisateur (Session #'.$caisseExistante->id.').');
            }

            $caisse = Caisse::create([
                'user_id' => $userId,
                'date_ouverture' => Carbon::now(),
                'fonds_initial' => round($fondsInitial, 2),
                'total_entrees' => 0.0,
                'total_sorties' => 0.0,
                'solde_theorique' => round($fondsInitial, 2),
                'statut' => 'ouverte',
                'observation' => $observation,
            ]);

            $this->journalService->log(
                action: 'ouverture_caisse',
                module: 'caisse',
                objetType: Caisse::class,
                objetId: $caisse->id,
                description: 'Ouverture caisse #'.$caisse->id.' avec fonds initial '.$caisse->fonds_initial.' FCFA',
                nouvellesValeurs: $caisse->toArray()
            );

            return $caisse;
        });
    }

    /**
     * Clôture une session de caisse et calcule les écarts.
     */
    public function fermerCaisse(Caisse $caisse, float $soldePhysique, ?string $observation = null): Caisse
    {
        return DB::transaction(function () use ($caisse, $soldePhysique, $observation) {
            if ($caisse->statut === 'fermee') {
                throw new \InvalidArgumentException('Cette session de caisse est déjà fermée.');
            }

            $totalEntrees = (float) MouvementCaisse::where('caisse_id', $caisse->id)->where('type', 'entree')->where('statut', true)->sum('montant');
            $totalSorties = (float) MouvementCaisse::where('caisse_id', $caisse->id)->where('type', 'sortie')->where('statut', true)->sum('montant');
            $soldeTheorique = round((float) $caisse->fonds_initial + $totalEntrees - $totalSorties, 2);

            $ecart = round($soldePhysique - $soldeTheorique, 2);

            $caisse->update([
                'date_fermeture' => Carbon::now(),
                'total_entrees' => $totalEntrees,
                'total_sorties' => $totalSorties,
                'solde_theorique' => $soldeTheorique,
                'solde_physique' => round($soldePhysique, 2),
                'ecart' => $ecart,
                'statut' => 'fermee',
                'observation' => $observation ?? $caisse->observation,
            ]);

            $this->journalService->log(
                action: 'fermeture_caisse',
                module: 'caisse',
                objetType: Caisse::class,
                objetId: $caisse->id,
                description: 'Fermeture caisse #'.$caisse->id.' (Théorique: '.$soldeTheorique.', Physique: '.$soldePhysique.', Écart: '.$ecart.' FCFA)',
                nouvellesValeurs: $caisse->toArray()
            );

            return $caisse;
        });
    }

    /**
     * Enregistre un mouvement d'espèces et met à jour automatiquement la session de caisse courante.
     *
     * @param  Caisse  $caisse  Caisse ouverte concernée
     * @param  int  $userId  Identifiant de l'agent caissier
     * @param  string  $type  Type de mouvement ('entree' ou 'sortie')
     * @param  string  $origine  Motif / origine du flux
     * @param  string  $reference  Référence de pièce
     * @param  float  $montant  Montant du flux
     * @param  string|null  $description  Observations optionnelles
     */
    public function enregistrerMouvement(
        Caisse $caisse,
        int $userId,
        string $type,
        string $origine,
        string $reference,
        float $montant,
        ?string $description = null
    ): MouvementCaisse {
        if ($caisse->statut === 'fermee') {
            throw new \InvalidArgumentException('Une caisse fermée ne peut plus recevoir de mouvement.');
        }

        $mouvement = MouvementCaisse::create([
            'caisse_id' => $caisse->id,
            'user_id' => $userId,
            'type' => $type,
            'origine' => $origine,
            'reference' => $reference,
            'montant' => round($montant, 2),
            'date_mouvement' => Carbon::now(),
            'description' => $description,
            'statut' => true,
        ]);

        if ($type === 'entree') {
            $caisse->increment('total_entrees', round($montant, 2));
        } else {
            $caisse->increment('total_sorties', round($montant, 2));
        }

        $soldeActualise = $this->calculerSoldeTheorique($caisse->fresh());
        $caisse->update(['solde_theorique' => $soldeActualise]);

        return $mouvement;
    }
}
