<?php

namespace App\Http\Controllers;

use App\Models\JournalActivite;
use App\Traits\HasPeriodFilter;

class JournalActiviteController extends Controller
{
    use HasPeriodFilter;

    /**
     * Affiche le journal d'audit et la traçabilité des opérations des utilisateurs filtrés par période.
     */
    public function index()
    {
        $period = request('periode', 'tous');
        $customStart = request('date_debut');
        $customEnd = request('date_fin');

        $filter = $this->getPeriodDates($period, $customStart, $customEnd);

        $query = JournalActivite::with('user')->latest('created_at');

        $this->applyDateFilter($query, $filter['start'], $filter['end'], 'created_at');

        $journalActivites = $query->get();
        $currentPeriod = $filter['period'];
        $periodLabel = $filter['label'];

        return view('journalactivites.index', compact('journalActivites', 'currentPeriod', 'periodLabel'));
    }

    /**
     * Affiche la fiche détaillée d'une entrée de journal.
     */
    public function show(JournalActivite $journalactivite)
    {
        $journalactivite->load('user');

        return view('journalactivites.show', compact('journalactivite'));
    }
}
