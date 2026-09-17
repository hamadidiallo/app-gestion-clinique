@extends('layout')

@section('title', 'Répertoire des Patients - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête du module avec titre, sous-titre et actions rapides --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Accueil & Médical</span>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Répertoire Actif</span>
            </div>
            <h1 class="h3 fw-bold mb-0 text-dark">Fichier des Patients</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('patient.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i data-lucide="user-plus" class="lucide-sm"></i>
                <span>Nouveau Patient</span>
            </a>
            <a href="{{ route('carteassurance.create') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="shield-plus" class="lucide-sm"></i>
                <span>Associer Assurance</span>
            </a>
        </div>
    </div>

    {{-- Composant de Filtrage par Période, Recherche et Statut --}}
    <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" :statuses="$statuses" />

    {{-- Cartes de synthèse des effectifs patients (Style doc/Clinique.dc.html) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e2f1ef; color: #0f766e;">
                        <i data-lucide="users" class="lucide"></i>
                    </div>
                    <span class="badge badge-info-pill">Total</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $patients->count() }}</div>
                <div class="small text-muted">Patients enregistrés</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e3f3ee; color: #0f6b5f;">
                        <i data-lucide="shield-check" class="lucide"></i>
                    </div>
                    <span class="badge badge-success-pill">Tiers payant</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $patients->where('statut', 'assure')->count() }}</div>
                <div class="small text-muted">Patients couverts (Assurés)</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #fdf0d5; color: #8a5712;">
                        <i data-lucide="wallet" class="lucide"></i>
                    </div>
                    <span class="badge badge-warning-pill">Comptant</span>
                </div>
                <div class="font-mono fs-4 fw-bold text-dark mb-1">{{ $patients->where('statut', '!=', 'assure')->count() }}</div>
                <div class="small text-muted">Paiement direct (Privé)</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                        <i data-lucide="user-check" class="lucide"></i>
                    </div>
                    <span class="badge bg-light text-muted border">Répartition</span>
                </div>
                <div class="font-mono fs-5 fw-bold text-dark mb-1">
                    {{ $patients->where('sexe', 'M')->count() }} <small class="text-muted fw-normal">H</small> · {{ $patients->where('sexe', 'F')->count() }} <small class="text-muted fw-normal">F</small>
                </div>
                <div class="small text-muted">Hommes / Femmes</div>
            </div>
        </div>
    </div>

    {{-- Tableau de données moderne stylisé (doc/Clinique.dc.html) --}}
    <div class="card overflow-hidden">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="contact-2" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <h5 class="card-title mb-0 fw-bold text-dark">Liste des Dossiers Patients</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <x-export-buttons table-id="patientsTable" title="Répertoire des Patients" filename="patients" />
                <span class="badge bg-light text-muted border font-mono">{{ $patients->count() }} patient(s)</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="patientsTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 90px;">Matricule</th>
                            <th>Patient</th>
                            <th>Sexe</th>
                            <th>Téléphone</th>
                            <th>Statut Assurance</th>
                            <th>Inscription</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td class="ps-3">
                                    <span class="font-mono text-muted small">#{{ $patient->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; background: #e2f1ef; color: #0f766e; font-size: 0.75rem;">
                                            {{ strtoupper(substr($patient->nom, 0, 1)) }}{{ strtoupper(substr($patient->prenom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $patient->nom }} {{ $patient->prenom }}</div>
                                            <small class="text-muted">{{ $patient->adresse ?? 'Adresse non précisée' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($patient->sexe === 'M')
                                        <span class="badge badge-info-pill">H</span>
                                    @else
                                        <span class="badge badge-warning-pill">F</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono text-dark">{{ $patient->telephone ?? '—' }}</span>
                                </td>
                                <td>
                                    @php $carteActive = $patient->cartesAssurance ? $patient->cartesAssurance->where('statut', true)->first() : null; @endphp
                                    @if ($patient->statut === 'assure')
                                        @if ($carteActive && $carteActive->assurance)
                                            <span class="badge badge-success-pill d-inline-flex align-items-center gap-1" title="Réf: {{ $carteActive->reference }}">
                                                <i data-lucide="shield-check" class="lucide-sm"></i>
                                                <span>{{ $carteActive->assurance->nom }}</span>
                                                <span class="font-mono fw-bold">({{ (float)$carteActive->taux_couverture }}%)</span>
                                            </span>
                                        @else
                                            <span class="badge badge-success-pill d-inline-flex align-items-center gap-1">
                                                <i data-lucide="shield-check" class="lucide-sm"></i>
                                                <span>Assuré</span>
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-muted border">
                                            Privé / Comptant
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono small text-muted">
                                        {{ $patient->created_at ? $patient->created_at->format('d/m/Y') : '—' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tickets.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" title="Nouveau Ticket Facture">
                                            <i data-lucide="receipt" class="lucide-sm"></i>
                                            <span>Ticket</span>
                                        </a>
                                        <a href="{{ route('carteassurance.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-secondary" title="Assurance">
                                            <i data-lucide="shield-plus" class="lucide-sm"></i>
                                        </a>
                                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary" title="Voir Fiche">
                                            <i data-lucide="eye" class="lucide-sm"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i data-lucide="edit-3" class="lucide-sm"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePatientModal{{ $patient->id }}" title="Supprimer">
                                            <i data-lucide="trash-2" class="lucide-sm"></i>
                                        </button>
                                    </div>

                                    {{-- Modale de confirmation de suppression --}}
                                    <div class="modal fade" id="deletePatientModal{{ $patient->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger text-white py-2">
                                                    <h6 class="modal-title d-flex align-items-center gap-2">
                                                        <i data-lucide="alert-triangle" class="lucide-sm"></i>
                                                        <span>Confirmation de suppression</span>
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start py-4">
                                                    Êtes-vous certain de vouloir supprimer le dossier du patient <strong>{{ $patient->nom }} {{ $patient->prenom }}</strong> ?
                                                </div>
                                                <div class="modal-footer bg-light py-2">
                                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('patients.destroy', $patient) }}" method="post" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer définitivement</button>
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
                                    <i data-lucide="users" class="lucide-lg d-block mx-auto mb-2 text-muted opacity-50"></i>
                                    Aucun patient inscrit pour cette sélection.
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
