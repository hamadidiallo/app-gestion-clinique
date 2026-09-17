<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Affiche la liste de tous les services médicaux de la clinique.
     */
    public function index()
    {
        // Récupère les services avec le nombre d'actes associés, triés du plus récent au plus ancien
        $services = Service::withCount('actes')->latest()->get();

        // Retourne la vue d'index des services en lui transmettant la liste
        return view('services.index', compact('services'));
    }

    /**
     * Recherche les services par leur nom ou leur code pour l'autocomplétion (format JSON).
     */
    public function search(Request $request)
    {
        $rawQuery = $request->input('q', '');
        if (is_array($rawQuery)) {
            $rawQuery = implode(' ', array_filter($rawQuery, fn ($i) => is_string($i) || is_numeric($i)));
        }
        $query = trim((string) $rawQuery);

        // Si le terme de recherche est vide, on retourne un tableau JSON vide
        if (empty($query)) {
            return response()->json([]);
        }

        // Effectue la recherche sur le nom ou le code du service actif
        $services = Service::where('statut', true)
            ->where(function ($q) use ($query) {
                $q->where('nom', 'LIKE', "%{$query}%")
                    ->orWhere('code', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'nom', 'code']);

        // Formate les résultats pour la réponse JSON
        $formatted = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'nom' => $service->nom,
                'code' => $service->code,
                'label' => $service->nom.' ('.$service->code.')',
            ];
        });

        // Retourne la réponse JSON pour la requête AJAX
        return response()->json($formatted);
    }

    /**
     * Affiche le formulaire de création d'un nouveau service.
     */
    public function create()
    {
        // Tableau des options de statut pour la liste déroulante (1 => Actif, 0 => Inactif)
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Retourne la vue de création de service
        return view('services.create', compact('statuts'));
    }

    /**
     * Enregistre un nouveau service médical en base de données.
     */
    public function store(ServiceRequest $request)
    {
        // Extrait uniquement les données validées par la classe ServiceRequest
        $validated = $request->validated();

        // Crée le nouveau service dans la base de données
        Service::create($validated);

        // Redirige vers la liste des services avec un message de confirmation
        return to_route('services.index')->with('alert', 'Création du service réussie');
    }

    /**
     * Affiche les détails d'un service médical spécifique ainsi que ses tarifs associés.
     */
    public function show(Service $service)
    {
        // Charge la relation avec les actes rattachés à ce service
        $service->load('actes');

        // Retourne la vue de détails du service
        return view('services.show', compact('service'));
    }

    /**
     * Affiche le formulaire de modification d'un service existant.
     */
    public function edit(Service $service)
    {
        // Options de statut pour la liste déroulante d'édition
        $statuts = [
            '1' => 'Actif',
            '0' => 'Inactif',
        ];

        // Retourne la vue d'édition pré-remplie avec le service sélectionné
        return view('services.edit', compact('service', 'statuts'));
    }

    /**
     * Met à jour les informations d'un service en base de données.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        // Récupère les données validées envoyées par le formulaire d'édition
        $validated = $request->validated();

        // Applique les modifications sur l'enregistrement du service
        $service->update($validated);

        // Redirige vers la liste des services avec un message de succès
        return to_route('services.index')->with('alert', 'Modification du service réussie');
    }

    /**
     * Supprime un service médical de la base de données.
     */
    public function destroy(Service $service)
    {
        // Vérifie si le service possède des actes associés avant de supprimer
        if ($service->actes()->exists()) {
            // Redirige avec un message d'erreur si des actes sont encore rattachés à ce service
            return to_route('services.index')->with('alert', 'Impossible de supprimer ce service car des actes y sont rattachés.');
        }

        // Supprime l'enregistrement du service de la base de données
        $service->delete();

        // Redirige vers la liste des services avec un message de confirmation de suppression
        return to_route('services.index')->with('alert', 'Suppression du service réussie');
    }
}
