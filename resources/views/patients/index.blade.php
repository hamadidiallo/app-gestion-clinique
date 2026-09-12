@extends('layout')

@section('title', 'Répertoire des Patients - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module avec titre, sous-titre et actions rapides --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i class="bi bi-people-fill me-2"></i>Répertoire des Patients
            </h1>
            <p class="text-muted mb-0">Gestion administrative des dossiers médicaux et coordonnées des patients.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('patient.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill"></i> Nouveau Patient
            </a>
            <a href="{{ route('carteassurance.create') }}" class="btn btn-outline-info fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-card-heading"></i> Associer Assurance
            </a>
        </div>
    </div>

    {{-- Composant de Filtrage par Période, Recherche et Statut --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" :statuses="$statuses" />

    {{-- Cartes de synthèse des effectifs patients --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                        <i class="bi bi-person-vcard fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Patients Enregistrés</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $patients->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Patients Assurés</span>
                        <h4 class="fw-bold mb-0 text-success">{{ $patients->where('statut', 'assure')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-secondary">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary-subtle text-secondary p-3 rounded-circle me-3">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Paiement Direct (Privé)</span>
                        <h4 class="fw-bold mb-0 text-secondary">{{ $patients->where('statut', '!=', 'assure')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-circle me-3">
                        <i class="bi bi-gender-ambiguous fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Hommes / Femmes</span>
                        <h4 class="fw-bold mb-0 text-info">
                            {{ $patients->where('sexe', 'M')->count() }} H / {{ $patients->where('sexe', 'F')->count() }} F
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau de données moderne stylisé --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-list-task text-primary me-2"></i>Fichier des Patients
            </h5>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="patientsTable" title="Répertoire des Patients" filename="patients" />
                <span class="badge bg-light text-dark border fs-7">{{ $patients->count() }} patient(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="patientsTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><i class="bi bi-hash text-muted me-1"></i>ID / Mat.</th>
                            <th><i class="bi bi-person-fill text-muted me-1"></i>Nom & Prénom</th>
                            <th><i class="bi bi-gender-ambiguous text-muted me-1"></i>Sexe</th>
                            <th><i class="bi bi-telephone text-muted me-1"></i>Téléphone</th>
                            <th><i class="bi bi-shield text-muted me-1"></i>Statut Assurance</th>
                            <th><i class="bi bi-clock-history text-muted me-1"></i>Date Inscription</th>
                            <th class="text-center pe-3"><i class="bi bi-gear text-muted me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-secondary font-monospace">#{{ $patient->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $patient->nom }} {{ $patient->prenom }}</div>
                                    <small class="text-muted">Adresse: {{ $patient->adresse ?? 'Non spécifiée' }}</small>
                                </td>
                                <td>
                                    @if($patient->sexe === 'M')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Hom. (M)</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Fem. (F)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $patient->telephone ?? '-' }}</span>
                                </td>
                                <td>
                                    @php $carteActive = $patient->cartesAssurance ? $patient->cartesAssurance->where('statut', true)->first() : null; @endphp
                                    @if ($patient->statut === 'assure')
                                        @if ($carteActive && $carteActive->assurance)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold" title="Réf: {{ $carteActive->reference }}">
                                                <i class="bi bi-shield-check me-1"></i> {{ $carteActive->assurance->nom }} ({{ (float)$carteActive->taux_couverture }}%)
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                                <i class="bi bi-shield-check me-1"></i> Assuré
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                            Non Assuré
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-dark fw-semibold">
                                        {{ $patient->created_at ? $patient->created_at->format('d/m/Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tickets.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-success" title="Créer un Ticket">
                                            <i class="bi bi-plus-circle me-1"></i> Ticket
                                        </a>
                                        <a href="{{ route('carteassurance.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-info" title="Associer / Carte Assurance">
                                            <i class="bi bi-card-heading"></i>
                                        </a>
                                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-primary" title="Voir Fiche">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePatientModal{{ $patient->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de suppression --}}
                                    <div class="modal fade" id="deletePatientModal{{ $patient->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    Êtes-vous sûr de vouloir supprimer définitivement le patient <strong>{{ $patient->nom }} {{ $patient->prenom }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('patients.destroy', $patient) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                    Aucun patient inscrit pour cette période. Cliquez sur "Nouveau Patient" pour en ajouter un.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
