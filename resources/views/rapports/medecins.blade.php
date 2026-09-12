@extends('layout')

@section('title', 'Rapport Rétrocession & Honoraires Médecins - CLINGEST')

@section('content')
<div class="container py-4">
    {{-- En-tête avec bouton retour --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-success font-weight-bold">👨‍⚕️ Rétrocession & Honoraires Médecins</h1>
            <p class="text-muted mb-0">Suivi analytique du partage des honoraires (Part Médecin vs Part Clinique).</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('rapports.index') }}" class="btn btn-outline-secondary">
                ⬅ Retour aux Rapports
            </a>
            <x-export-buttons table-id="rapportMedecinsTable" title="Rapport Rétrocession & Honoraires Médecins" filename="rapport_honoraires_medecins" />
        </div>
    </div>

    {{-- Formulaire de filtres par médecin et dates --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body">
            <form method="GET" action="{{ route('rapports.medecins') }}" class="row g-3 align-items-end">
                {{-- Sélection du médecin --}}
                <div class="col-md-4">
                    <label for="medecin_id" class="form-label font-weight-bold">Médecin Prestataire :</label>
                    <select name="medecin_id" id="medecin_id" class="form-select">
                        <option value="">-- Tous les Médecins --</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ $medecinId == $medecin->id ? 'selected' : '' }}>
                                Dr. {{ $medecin->nom }} {{ $medecin->prenom }} ({{ $medecin->specialite ?? 'Généraliste' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date de début --}}
                <div class="col-md-3">
                    <label for="date_debut" class="form-label font-weight-bold">Date Début :</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ $dateDebut }}">
                </div>

                {{-- Date de fin --}}
                <div class="col-md-3">
                    <label for="date_fin" class="form-label font-weight-bold">Date Fin :</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ $dateFin }}">
                </div>

                {{-- Boutons d'action --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success w-100">Filtrer</button>
                    <a href="{{ route('rapports.medecins') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Synthèse des totaux d'honoraires --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white p-3 text-center border-start border-4 border-success">
                <span class="text-muted small">Total Part Dûe aux Médecins</span>
                <h4 class="text-success font-weight-bold mb-0">{{ number_format($totalPartMedecin, 0, ',', ' ') }} FBU</h4>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white p-3 text-center border-start border-4 border-info">
                <span class="text-muted small">Total Part Conservée par la Clinique</span>
                <h4 class="text-info font-weight-bold mb-0">{{ number_format($totalPartClinique, 0, ',', ' ') }} FBU</h4>
            </div>
        </div>
    </div>

    {{-- Tableau des rémunérations et rétrocessions calculées --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="rapportMedecinsTable" class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date Calcul</th>
                            <th>Médecin</th>
                            <th>Patient / Prestation</th>
                            <th class="text-center">Taux Médecin</th>
                            <th class="text-end text-success">Part Médecin (FBU)</th>
                            <th class="text-end text-info">Part Clinique (FBU)</th>
                            <th class="text-center">Statut Paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remunerations as $rem)
                            <tr>
                                <td>{{ $rem->date_prestation ? $rem->date_prestation->format('d/m/Y H:i') : ($rem->created_at ? $rem->created_at->format('d/m/Y H:i') : '-') }}</td>
                                <td class="fw-bold">
                                    Dr. {{ $rem->medecin->nom ?? 'N/A' }} {{ $rem->medecin->prenom ?? '' }}
                                </td>
                                <td>
                                    <small class="d-block fw-bold text-dark">
                                        Patient: {{ $rem->patient->nom ?? 'Inconnu' }} {{ $rem->patient->prenom ?? '' }}
                                    </small>
                                    <small class="text-muted">
                                        Acte: {{ $rem->service->nom ?? $rem->type ?? 'Acte médical' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        {{ number_format($rem->pourcentage_medecin ?? 50, 0) }}%
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($rem->part_medecin ?? 0, 0, ',', ' ') }} FBU
                                </td>
                                <td class="text-end fw-bold text-info">
                                    {{ number_format($rem->part_clinique ?? 0, 0, ',', ' ') }} FBU
                                </td>
                                <td class="text-center">
                                    @if($rem->statut)
                                        <span class="badge bg-success">Payé / Validé</span>
                                    @else
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Aucune rétrocession enregistrée pour cette sélection.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">TOTAUX PERIODE :</td>
                            <td class="text-end text-success fs-6">{{ number_format($totalPartMedecin, 0, ',', ' ') }} FBU</td>
                            <td class="text-end text-info fs-6">{{ number_format($totalPartClinique, 0, ',', ' ') }} FBU</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end">
            {{ $remunerations->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
