@extends('layout')

@section('title', 'Générer un Décompte de Rémunération - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            {{-- En-tête --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <a href="{{ route('remunerations.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                            <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Retour aux rémunérations
                        </a>
                    </div>
                    <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i data-lucide="calculator" class="text-teal"></i>Générer une Fiche de Rémunération
                    </h1>
                    <p class="text-muted mb-0 small">Calcul automatique des honoraires et rétrocessions d'un praticien sur une période donnée.</p>
                </div>
            </div>

            {{-- Carte Formulaire --}}
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="user-check" class="text-teal"></i>
                        <h6 class="mb-0 fw-bold text-dark">Paramètres du Décompte Médical</h6>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('remunerations.store') }}" method="POST" id="remunerationForm">
                        @csrf

                        <div class="row g-4">
                            {{-- Sélection du Médecin --}}
                            <div class="col-md-6">
                                <label for="medecin_id" class="form-label fw-bold small text-muted text-uppercase">
                                    Médecin Praticien <span class="text-danger">*</span>
                                </label>
                                <select name="medecin_id" id="medecin_id" class="form-select select2-enable @error('medecin_id') is-invalid @enderror" required>
                                    <option value="">-- Choisir un praticien --</option>
                                    @foreach($medecins as $m)
                                        <option value="{{ $m->id }}" 
                                            data-type="{{ $m->type_remuneration ?? 'pourcentage' }}"
                                            data-taux="{{ $m->pourcentage ?? 50 }}"
                                            data-fixe="{{ $m->salaire_fixe ?? 0 }}"
                                            {{ old('medecin_id') == $m->id ? 'selected' : '' }}>
                                            Dr. {{ $m->prenom }} {{ $m->nom }} ({{ $m->specialite ?? 'Général' }}) - {{ $m->type_remuneration === 'salaire_fixe' ? 'Salaire Fixe' : ($m->pourcentage ?? 50) . '%' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('medecin_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small" id="medecinHelp">
                                    Sélectionnez le médecin pour lequel établir la paie.
                                </div>
                            </div>

                            {{-- Agent validateur --}}
                            <div class="col-md-6">
                                <label for="user_id" class="form-label fw-bold small text-muted text-uppercase">
                                    Agent Responsable / Validateur
                                </label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name ?? $user->nom }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Période Début & Fin --}}
                            <div class="col-md-6">
                                <label for="periode_debut" class="form-label fw-bold small text-muted text-uppercase">
                                    Date de Début <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="periode_debut" id="periode_debut" 
                                    class="form-control @error('periode_debut') is-invalid @enderror" 
                                    value="{{ old('periode_debut', date('Y-m-01')) }}" required>
                                @error('periode_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="periode_fin" class="form-label fw-bold small text-muted text-uppercase">
                                    Date de Fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="periode_fin" id="periode_fin" 
                                    class="form-control @error('periode_fin') is-invalid @enderror" 
                                    value="{{ old('periode_fin', date('Y-m-t')) }}" required>
                                @error('periode_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Raccourcis de sélection de dates --}}
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="text-muted small fw-semibold">Raccourcis période :</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setDateRange('current_month')">Mois en cours</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setDateRange('last_month')">Mois dernier</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="setDateRange('current_week')">Cette semaine</button>
                                </div>
                            </div>
                        </div>

                        {{-- Carte de Prévisualisation Temps Réel des Actes Réalisés --}}
                        <div class="mt-4 pt-4 border-top" id="previewContainer">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i data-lucide="sparkles" class="text-teal" style="width: 18px; height: 18px;"></i>
                                    Prévisualisation Automatique des Soins (Prestations)
                                </h6>
                                <span class="badge bg-light text-dark border small" id="previewStatus">En attente de sélection</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6 col-md-3">
                                    <div class="bg-light p-3 rounded-3 text-center border">
                                        <span class="text-muted small d-block mb-1">Soins / Actes Détectés</span>
                                        <h4 class="fw-bold mb-0 text-dark font-mono" id="previewNbPrestations">-</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="bg-light p-3 rounded-3 text-center border">
                                        <span class="text-muted small d-block mb-1">Base Brute Facturée</span>
                                        <h4 class="fw-bold mb-0 text-dark font-mono" id="previewBase">-</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="bg-teal-subtle p-3 rounded-3 text-center border border-teal-subtle">
                                        <span class="text-teal small d-block mb-1 fw-semibold">Part Praticien (Estimée)</span>
                                        <h4 class="fw-bold mb-0 text-teal font-mono" id="previewPartMedecin">-</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="bg-primary-subtle p-3 rounded-3 text-center border border-primary-subtle">
                                        <span class="text-primary small d-block mb-1 fw-semibold">Part Clinique</span>
                                        <h4 class="fw-bold mb-0 text-primary font-mono" id="previewPartClinique">-</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-4 pt-3 border-top">
                            <a href="{{ route('remunerations.index') }}" class="btn btn-outline-secondary px-3">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-teal fw-semibold px-4 py-2 d-flex align-items-center gap-2 shadow-sm">
                                <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                                <span>Générer le Décompte</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setDateRange(type) {
        const now = new Date();
        let start, end;

        if (type === 'current_month') {
            start = new Date(now.getFullYear(), now.getMonth(), 1);
            end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        } else if (type === 'last_month') {
            start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            end = new Date(now.getFullYear(), now.getMonth(), 0);
        } else if (type === 'current_week') {
            const day = now.getDay() || 7;
            start = new Date(now);
            start.setDate(now.getDate() - day + 1);
            end = new Date(now);
            end.setDate(now.getDate() - day + 7);
        }

        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        document.getElementById('periode_debut').value = formatDate(start);
        document.getElementById('periode_fin').value = formatDate(end);
        updatePreview();
    }

    async function updatePreview() {
        const medecinId = document.getElementById('medecin_id').value;
        const debut = document.getElementById('periode_debut').value;
        const fin = document.getElementById('periode_fin').value;
        const statusBadge = document.getElementById('previewStatus');

        if (!medecinId || !debut || !fin) {
            return;
        }

        statusBadge.textContent = 'Calcul en cours...';
        statusBadge.className = 'badge bg-warning-subtle text-warning border small';

        try {
            const response = await fetch(`{{ route('remunerations.preview') }}?medecin_id=${medecinId}&periode_debut=${debut}&periode_fin=${fin}`);
            const result = await response.json();

            if (result.success) {
                const data = result.data;
                document.getElementById('previewNbPrestations').textContent = data.nb_prestations;
                document.getElementById('previewBase').textContent = new Intl.NumberFormat('fr-FR').format(data.montant_base) + ' FCFA';
                document.getElementById('previewPartMedecin').textContent = new Intl.NumberFormat('fr-FR').format(data.montant_medecin) + ' FCFA';
                document.getElementById('previewPartClinique').textContent = new Intl.NumberFormat('fr-FR').format(data.montant_clinique) + ' FCFA';

                statusBadge.textContent = `${data.nb_prestations} prestation(s) trouvée(s)`;
                statusBadge.className = 'badge bg-success-subtle text-success border small';
            }
        } catch (e) {
            statusBadge.textContent = 'Erreur prévisualisation';
            statusBadge.className = 'badge bg-danger-subtle text-danger border small';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('medecin_id').addEventListener('change', updatePreview);
        document.getElementById('periode_debut').addEventListener('change', updatePreview);
        document.getElementById('periode_fin').addEventListener('change', updatePreview);

        if (document.getElementById('medecin_id').value) {
            updatePreview();
        }
    });
</script>
@endsection
