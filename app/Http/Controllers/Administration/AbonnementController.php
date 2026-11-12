<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AbonnementController extends Controller
{
    /**
     * Affiche les formules d'abonnement et le statut de la licence actuelle de la clinique.
     */
    public function index(): View
    {
        $user = auth()->user();
        $clinique = $user->clinique;

        $formules = config('medigest.formules');

        return view('administration.abonnement.index', compact('clinique', 'formules'));
    }
}
