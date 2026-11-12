@extends('layout')

@section('title', 'Consultations Médicales & Prise de Constantes')

@section('content')
<div class="container-fluid py-3">
    {{-- En-tête avec titre et bouton de nouvelle consultation --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i data-lucide="heart-pulse" class="me-2"></i>Dossier Médical : Consultations & Constantes
            </h1>
            <p class="text-muted mb-0">Relevé des constantes vitales, examen clinique, diagnostic et prescription d'ordonnances.</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('consultations.create') }}" class="btn btn-primary shadow-sm fw-bold">
                <i data-lucide="plus-circle" class="me-1"></i> Nouvelle Consultation / Constantes
            </a>
        </div>
    </div>

    {{-- Alertes de session --}}
    @if(session('alert'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i data-lucide="check-circle" class="me-2"></i>{{ session('alert') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    {{-- Filtres & Recherche --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('consultations.index') }}" method="GET" class="row g-2 align-items-center">
                {{-- Barre de recherche --}}
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="{{ $peutVoirDossierMedical ? 'Rechercher réf, patient, médecin, diagnostic...' : 'Rechercher réf, patient, médecin...' }}" value="{{ request('q') }}">
                    </div>
                </div>

                {{-- Filtre Période --}}
                <div class="col-md-3">
                    <select name="periode" class="form-select" onchange="this.form.submit()">
                        <option value="tous" {{ $currentPeriod == 'tous' ? 'selected' : '' }}>Toutes les dates</option>
                        <option value="aujourdhui" {{ $currentPeriod == 'aujourdhui' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="semaine" {{ $currentPeriod == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="mois" {{ $currentPeriod == 'mois' ? 'selected' : '' }}>Ce mois</option>
                    </select>
                </div>

                {{-- Filtre Médecin --}}
                <div class="col-md-3">
                    <select name="medecin_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les médecins</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ request('medecin_id') == $medecin->id ? 'selected' : '' }}>
                                Dr {{ $medecin->nom }} {{ $medecin->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-secondary w-100"><i data-lucide="filter"></i> Filtrer</button>
                    @if(request()->hasAny(['q', 'periode', 'medecin_id']))
                        <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary" title="Réinitialiser"><i data-lucide="x"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau des consultations --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i data-lucide="list-checks" class="me-2"></i>Historique des consultations ({{ $consultations->count() }})</h6>
            <span class="badge bg-light text-muted">{{ $periodLabel }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Réf / Date</th>
                        <th>Patient</th>
                        <th>Constantes relevées</th>
                        <th>Médecin</th>
                        <th>Diagnostic / Motif</th>
                        <th>Ordonnance</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultations as $item)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-primary font-monospace">{{ $item->reference }}</span>
                                <small class="d-block text-muted">{{ $item->date_consultation->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                @if($item->patient)
                                    <a href="{{ route('patients.show', $item->patient) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $item->patient->nom }} {{ $item->patient->prenom }}
                                    </a>
                                    <small class="d-block text-muted">
                                        {{ $item->patient->sexe == 'M' ? 'Homme' : 'Femme' }}
                                        @if($item->patient->assurance)
                                            • <span class="badge bg-info text-dark">{{ $item->patient->assurance->nom }}</span>
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">Inconnu</span>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    @if($item->tension_arterielle)
                                        <span class="badge bg-secondary me-1" title="Tension Artérielle">TA: {{ $item->tension_arterielle }}</span>
                                    @endif
                                    @if($item->temperature)
                                        <span class="badge bg-warning text-dark me-1" title="Température">T°: {{ $item->temperature }}°C</span>
                                    @endif
                                    @if($item->poids)
                                        <span class="badge bg-light text-dark border me-1" title="Poids">{{ $item->poids }} kg</span>
                                    @endif
                                    @if(!$item->tension_arterielle && !$item->temperature && !$item->poids)
                                        <span class="text-muted fst-italic">Non renseignées</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($item->medecin)
                                    <span class="fw-semibold">Dr {{ $item->medecin->nom }} {{ $item->medecin->prenom }}</span>
                                    <small class="d-block text-muted">{{ $item->medecin->specialite ?? 'Généraliste' }}</small>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                <strong class="d-block text-dark">{{ Str::limit($item->motif_consultation, 30) }}</strong>
                                @if($peutVoirDossierMedical && $item->diagnostic)
                                    <small class="text-muted"><i data-lucide="activity" class="text-danger me-1"></i>{{ Str::limit($item->diagnostic, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                @if(! $peutVoirDossierMedical)
                                    <span class="badge bg-light text-muted border">&mdash;</span>
                                @elseif($item->ordonnance)
                                    <a href="{{ route('consultations.print-ordonnance', $item) }}" target="_blank" class="badge bg-success text-decoration-none py-2 px-2" title="Imprimer Ordonnance">
                                        <i data-lucide="printer" class="me-1"></i> {{ $item->ordonnance->reference }}
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border">Aucune</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    @if($peutVoirDossierMedical)
                                        <a href="{{ route('consultations.show', $item) }}" class="btn btn-outline-primary" title="Voir fiche complète">
                                            <i data-lucide="eye"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('consultations.edit', $item) }}" class="btn btn-outline-warning" title="Modifier">
                                        <i data-lucide="edit-3"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i data-lucide="inbox" class="fs-1 d-block mb-2 text-secondary"></i>
                                Aucune consultation enregistrée pour ces critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
