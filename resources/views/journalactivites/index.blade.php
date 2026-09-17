@extends('layout')

@section('title', 'Journal d\'Activité & Audit - CLINGEST')

@section('content')
    <div class="container-fluid p-0">
        {{-- En-tête --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 text-primary font-weight-bold mb-1">
                    <i data-lucide="clock" class="me-2"></i>Journal d'Audit & Traçabilité des Actions
                </h1>
                <p class="text-muted mb-0">Historique des connexions, modifications et évènements enregistrés.</p>
            </div>
        </div>

        {{-- Composant de Filtrage par Période --}}
        <x-period-filter :currentPeriod="$currentPeriod" :periodLabel="$periodLabel" />

        {{-- Tableau du journal d'activité --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i data-lucide="shield-alert" class="text-primary me-2"></i>Logs d'Audit Système
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <x-export-buttons table-id="journalActivitesTable" title="Journal d'Audit & Traçabilité" filename="journal_activites" />
                    <span class="badge bg-light text-dark border fs-7">{{ $journalActivites->count() }} événement(s)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="journalActivitesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Date & Heure</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Objet Cible</th>
                                <th>Adresse IP</th>
                                <th class="text-center pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($journalActivites as $log)
                                <tr>
                                    <th scope="row" class="ps-3">{{ $log->id }}</th>
                                    <td>
                                        <small class="fw-semibold text-dark">
                                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : ($log->date_action ? \Carbon\Carbon::parse($log->date_action)->format('d/m/Y H:i:s') : '-') }}
                                        </small>
                                    </td>
                                    <td><strong>{{ $log->user->name ?? $log->user->nom ?? 'Système' }}</strong></td>
                                    <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">{{ ucfirst($log->action ?? 'Action') }}</span></td>
                                    <td><span class="badge bg-secondary-subtle text-dark border px-3 py-1 rounded-pill">{{ ucfirst($log->module ?? 'Général') }}</span></td>
                                    <td>{{ $log->objet_type }} #{{ $log->objet_id }}</td>
                                    <td><code>{{ $log->adresse_ip ?? '127.0.0.1' }}</code></td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('journalactivites.show', $log) }}" class="btn btn-sm btn-outline-info" title="Détails">
                                            <i data-lucide="eye"></i> Détails
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i data-lucide="clock" class="fs-1 d-block mb-2 text-secondary"></i>
                                        Aucune activité enregistrée pour cette période.
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
