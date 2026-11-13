<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les modèles ont été rangés en sous-dossiers (App\Models\Caisse → App\Models\Caisse\Caisse…).
     * Le journal d'activité conserve le nom de classe de l'objet concerné : on aligne les
     * anciennes lignes sur les nouveaux noms pour que l'historique reste cohérent.
     */
    private const GROUPES = [
        'Administration' => ['User', 'Role', 'Clinique', 'Invitation', 'JournalActivite'],
        'Patients' => ['Patient', 'DossierMedical'],
        'Medical' => ['Consultation', 'Ordonnance', 'OrdonnanceLigne', 'Acte', 'ActeParametre'],
        'Facturation' => ['Ticket', 'TicketDetail', 'Prestation', 'Service'],
        'Caisse' => ['Caisse', 'MouvementCaisse', 'Paiement', 'ModePaiement', 'Dette'],
        'Comptabilite' => ['Recette', 'Depense', 'CategorieDepense'],
        'Assurances' => ['Assurance', 'CarteAssurance', 'ReglePartage'],
        'Personnel' => ['Medecin', 'Remuneration'],
    ];

    public function up(): void
    {
        foreach ($this->correspondances() as $ancien => $nouveau) {
            DB::table('journal_activites')->where('objet_type', $ancien)->update(['objet_type' => $nouveau]);
        }
    }

    public function down(): void
    {
        foreach ($this->correspondances() as $ancien => $nouveau) {
            DB::table('journal_activites')->where('objet_type', $nouveau)->update(['objet_type' => $ancien]);
        }
    }

    /**
     * @return array<string, string> ancien nom de classe => nouveau
     */
    private function correspondances(): array
    {
        $correspondances = [];
        foreach (self::GROUPES as $groupe => $modeles) {
            foreach ($modeles as $modele) {
                $correspondances['App\\Models\\'.$modele] = 'App\\Models\\'.$groupe.'\\'.$modele;
            }
        }

        return $correspondances;
    }
};
