<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActeRequest;
use App\Models\Acte;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActeController extends Controller
{
    /**
     * Catégories disponibles pour les actes médicaux.
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'consultation' => 'Consultation',
        'chirurgie' => 'Acte Chirurgical',
        'imagerie' => 'Imagerie / Échographie',
        'biologie' => 'Laboratoire & Biologie',
        'soins' => 'Soins Infirmiers & Urgences',
        'exploration' => 'Exploration Fonctionnelle',
        'maternite' => 'Maternité / Accouchement',
        'autre' => 'Autre prestation',
    ];

    /**
     * Affiche la liste des actes médicaux avec filtres.
     */
    public function index(Request $request): View
    {
        $query = Acte::with('service');

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->input('categorie'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut') === '1');
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $actes = $query->latest()->paginate(15)->withQueryString();
        $services = Service::where('statut', true)->orderBy('nom')->get();
        $categories = self::CATEGORIES;

        // Statistiques globales
        $stats = [
            'total' => Acte::count(),
            'actifs' => Acte::where('statut', true)->count(),
            'categories' => Acte::distinct('categorie')->count('categorie'),
            'services' => Service::whereHas('actes')->count(),
        ];

        return view('actes.index', compact('actes', 'services', 'categories', 'stats'));
    }

    /**
     * Recherche d'actes en JSON pour l'autocomplétion (création de tickets, consultations).
     */
    public function search(Request $request): JsonResponse
    {
        $rawQuery = $request->input('q', '');
        if (is_array($rawQuery)) {
            $rawQuery = implode(' ', array_filter($rawQuery, fn ($i) => is_string($i) || is_numeric($i)));
        }
        $query = trim((string) $rawQuery);

        if (empty($query)) {
            return response()->json([]);
        }

        $acteQuery = Acte::with('service')
            ->where('statut', true)
            ->where(function ($q) use ($query) {
                $q->where('nom', 'LIKE', "%{$query}%")
                    ->orWhere('code', 'LIKE', "%{$query}%");
            });

        if ($request->filled('service_id')) {
            $acteQuery->where('service_id', $request->input('service_id'));
        }

        $actes = $acteQuery->limit(15)->get();

        $formatted = $actes->map(function (Acte $acte) {
            return [
                'id' => $acte->id,
                'code' => $acte->code,
                'nom' => $acte->nom,
                'categorie' => $acte->categorie,
                'categorie_libelle' => $acte->categorie_libelle,
                'service_id' => $acte->service_id,
                'service_nom' => $acte->service ? $acte->service->nom : null,
                'tarif_normal' => (float) $acte->tarif_normal,
                'tarif_amo' => $acte->tarif_amo !== null ? (float) $acte->tarif_amo : null,
                'tarif_specifique' => $acte->tarif_specifique !== null ? (float) $acte->tarif_specifique : null,
                'part_medecin_pourcentage' => (float) $acte->part_medecin_pourcentage,
                'part_clinique_pourcentage' => (float) $acte->part_clinique_pourcentage,
                'label' => "[{$acte->code}] {$acte->nom} (".number_format($acte->tarif_normal, 0, ',', ' ').' FCFA)',
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Affiche le formulaire de création d'un acte médical.
     */
    public function create(): View
    {
        $services = Service::where('statut', true)->orderBy('nom')->get();
        $categories = self::CATEGORIES;

        return view('actes.create', compact('services', 'categories'));
    }

    /**
     * Enregistre un nouvel acte médical en base.
     */
    public function store(ActeRequest $request): RedirectResponse
    {
        Acte::create($request->validated());

        return to_route('actes.index')->with('alert', 'Acte médical créé avec succès dans le catalogue.');
    }

    /**
     * Affiche la fiche détaillée d'un acte médical.
     */
    public function show(Acte $acte): View
    {
        $acte->load('service');

        return view('actes.show', compact('acte'));
    }

    /**
     * Affiche le formulaire d'édition d'un acte médical.
     */
    public function edit(Acte $acte): View
    {
        $services = Service::where('statut', true)->orderBy('nom')->get();
        $categories = self::CATEGORIES;

        return view('actes.edit', compact('acte', 'services', 'categories'));
    }

    /**
     * Met à jour un acte médical en base.
     */
    public function update(ActeRequest $request, Acte $acte): RedirectResponse
    {
        $acte->update($request->validated());

        return to_route('actes.index')->with('alert', 'Acte médical mis à jour avec succès.');
    }

    /**
     * Supprime un acte médical.
     */
    public function destroy(Acte $acte): RedirectResponse
    {
        $acte->delete();

        return to_route('actes.index')->with('alert', 'Acte médical supprimé avec succès.');
    }
}
