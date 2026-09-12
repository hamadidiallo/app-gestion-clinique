<?php

namespace App\Http\Controllers;

use App\Http\Requests\TarifRequest;
use App\Models\Service;
use App\Models\Tarif;

class TarifController extends Controller
{
    /**
     * Affiche la liste de tous les tarifs enregistrés dans la clinique.
     */
    public function index()
    {
        // Récupère les tarifs avec leurs services associés, triés du plus récent au plus ancien
        $tarifs = Tarif::with('service')->latest()->get();

        // Retourne la vue d'index des tarifs en lui passant la liste récupérée
        return view('tarifs.index', compact('tarifs'));
    }

    /**
     * Affiche le formulaire de création d'un tarif avec champ de recherche autocomplété.
     */
    public function create()
    {
        // Tableau d'options pour le statut du tarif (1 => Actif, 0 => Inactif)
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Récupère le service médical éventuellement pré-sélectionné en cas de retour d'erreur
        $selectedService = old('service_id') ? Service::find(old('service_id')) : null;

        // Retourne la vue de création de tarif avec les données de statut et de service sélectionné
        return view('tarifs.create', compact('statuts', 'selectedService'));
    }

    /**
     * Enregistre un nouveau tarif dans la base de données.
     */
    public function store(TarifRequest $request)
    {
        // Récupère les données validées soumises via le formulaire
        $validated = $request->validated();

        // Crée l'enregistrement du nouveau tarif en base de données
        Tarif::create($validated);

        // Redirige vers la liste des tarifs avec un message de succès
        return to_route('tarifs.index')->with('alert', 'Création du tarif réussie');
    }

    /**
     * Affiche les détails d'un tarif spécifique.
     */
    public function show(Tarif $tarif)
    {
        // Charge la relation avec le service médical rattaché à ce tarif
        $tarif->load('service');

        // Retourne la vue de présentation détaillée du tarif
        return view('tarifs.show', compact('tarif'));
    }

    /**
     * Affiche le formulaire de modification d'un tarif existant avec le service autocomplété.
     */
    public function edit(Tarif $tarif)
    {
        // Charge la relation avec le service courant
        $tarif->load('service');

        // Options pour le champ de sélection du statut
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Service sélectionné (soit issu de old() en cas d'erreur de validation, soit du tarif existant)
        $serviceId = old('service_id', $tarif->service_id);
        $selectedService = $serviceId ? Service::find($serviceId) : null;

        // Retourne la vue de modification avec le tarif et le service médical pré-rempli
        return view('tarifs.edit', compact('tarif', 'statuts', 'selectedService'));
    }

    /**
     * Met à jour les informations d'un tarif existant en base de données.
     */
    public function update(TarifRequest $request, Tarif $tarif)
    {
        // Extrait les champs validés de la requête d'édition
        $validated = $request->validated();

        // Met à jour le tarif sélectionné avec les données modifiées
        $tarif->update($validated);

        // Redirige vers la liste des tarifs avec un message de confirmation
        return to_route('tarifs.index')->with('alert', 'Modification du tarif réussie');
    }

    /**
     * Supprime un tarif de la base de données.
     */
    public function destroy(Tarif $tarif)
    {
        // Supprime l'enregistrement du tarif sélectionné
        $tarif->delete();

        // Redirige vers la liste des tarifs avec un message de succès
        return to_route('tarifs.index')->with('alert', 'Suppression du tarif réussie');
    }
}
