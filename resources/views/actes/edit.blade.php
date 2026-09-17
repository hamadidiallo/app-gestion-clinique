@extends('layout')

@section('title', 'Modifier l\'Acte Médical - ' . $acte->nom)

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('actes.index') }}" class="text-muted text-decoration-none small d-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 0.9rem; height: 0.9rem;"></i>
                    <span>Catalogue des Actes</span>
                </a>
            </div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="edit-3" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>Modifier l'Acte Médical</span>
            </h1>
            <p class="text-muted mb-0 small">Mise à jour des informations, tarifs et pourcentages d'honoraires pour cet acte.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('actes.show', $acte) }}" class="btn btn-outline-teal fw-medium d-flex align-items-center gap-2">
                <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                <span>Voir la Fiche</span>
            </a>
            <a href="{{ route('actes.index') }}" class="btn btn-outline-secondary fw-medium d-flex align-items-center gap-2">
                <i data-lucide="x" style="width: 1rem; height: 1rem;"></i>
                <span>Annuler</span>
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2 mb-2 fw-bold text-danger">
                <i data-lucide="alert-triangle" style="width: 1.2rem; height: 1.2rem;"></i>
                <span>Veuillez corriger les erreurs ci-dessous :</span>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <form action="{{ route('actes.update', $acte) }}" method="POST" id="acteForm">
                @csrf
                @method('PUT')

                {{-- Carte 1: Identification & Service --}}
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4" style="border: 1px solid #e6ebf0 !important;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i data-lucide="info" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                            <span>Identification & Rattachement Médical</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="service_id" class="form-label fw-semibold text-dark small">
                                    Service / Département Clinique <span class="text-danger">*</span>
                                </label>
                                <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required style="border-color: #cbd5e1;">
                                    <option value="">Sélectionner un service...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id', $acte->service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }} ({{ $service->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="categorie" class="form-label fw-semibold text-dark small">
                                    Catégorie d'Acte <span class="text-danger">*</span>
                                </label>
                                <select name="categorie" id="categorie" class="form-select @error('categorie') is-invalid @enderror" required style="border-color: #cbd5e1;">
                                    @foreach($categories as $key => $libelle)
                                        <option value="{{ $key }}" {{ old('categorie', $acte->categorie) == $key ? 'selected' : '' }}>
                                            {{ $libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="code" class="form-label fw-semibold text-dark small">
                                    Code de l'Acte <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="code" id="code" class="form-control font-mono @error('code') is-invalid @enderror" value="{{ old('code', $acte->code) }}" required style="border-color: #cbd5e1; text-transform: uppercase;">
                                <div class="form-text small text-muted">Identifiant unique dans la nomenclature.</div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="nom" class="form-label fw-semibold text-dark small">
                                    Libellé Officiel de l'Acte <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $acte->nom) }}" required style="border-color: #cbd5e1;">
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold text-dark small">
                                    Description & Protocoles Associés
                                </label>
                                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" style="border-color: #cbd5e1;">{{ old('description', $acte->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte 2: Tarification (FCFA) & Prise en Charge AMO --}}
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4" style="border: 1px solid #e6ebf0 !important;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i data-lucide="coins" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                            <span>Grille Tarifaire (FCFA)</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="tarif_normal" class="form-label fw-semibold text-dark small">
                                    Tarif Normal (Privé / Comptant) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="100" min="0" name="tarif_normal" id="tarif_normal" class="form-control font-mono @error('tarif_normal') is-invalid @enderror" value="{{ old('tarif_normal', (int)$acte->tarif_normal) }}" required style="border-color: #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted small">FCFA</span>
                                    @error('tarif_normal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Tarif appliqué aux patients non assurés.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="tarif_amo" class="form-label fw-semibold text-dark small">
                                    Tarif Conventionné AMO Mali
                                </label>
                                <div class="input-group">
                                    <input type="number" step="100" min="0" name="tarif_amo" id="tarif_amo" class="form-control font-mono @error('tarif_amo') is-invalid @enderror" value="{{ old('tarif_amo', $acte->tarif_amo ? (int)$acte->tarif_amo : '') }}" placeholder="Optionnel" style="border-color: #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted small">FCFA</span>
                                    @error('tarif_amo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Base de remboursement CANAM / AMO.</div>
                            </div>

                            <div class="col-md-4">
                                <label for="tarif_specifique" class="form-label fw-semibold text-dark small">
                                    Tarif Assurances Privées
                                </label>
                                <div class="input-group">
                                    <input type="number" step="100" min="0" name="tarif_specifique" id="tarif_specifique" class="form-control font-mono @error('tarif_specifique') is-invalid @enderror" value="{{ old('tarif_specifique', $acte->tarif_specifique ? (int)$acte->tarif_specifique : '') }}" placeholder="Optionnel" style="border-color: #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted small">FCFA</span>
                                    @error('tarif_specifique')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Tarif négocié (INPS, NSIA, SAHAM, etc.).</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Carte 3: Répartition des Honoraires & Statut --}}
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4" style="border: 1px solid #e6ebf0 !important;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <i data-lucide="pie-chart" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                            <span>Répartition des Honoraires & Statut</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="part_medecin_pourcentage" class="form-label fw-semibold text-dark small">
                                    Part Praticien / Médecin (%) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.5" min="0" max="100" name="part_medecin_pourcentage" id="part_medecin_pourcentage" class="form-control font-mono @error('part_medecin_pourcentage') is-invalid @enderror" value="{{ old('part_medecin_pourcentage', (float)$acte->part_medecin_pourcentage) }}" required style="border-color: #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted">%</span>
                                    @error('part_medecin_pourcentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Quote-part reversée au médecin.</div>
                            </div>

                            <div class="col-md-5">
                                <label for="part_clinique_pourcentage" class="form-label fw-semibold text-dark small">
                                    Part Clinique / Établissement (%) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.5" min="0" max="100" name="part_clinique_pourcentage" id="part_clinique_pourcentage" class="form-control font-mono @error('part_clinique_pourcentage') is-invalid @enderror" value="{{ old('part_clinique_pourcentage', (float)$acte->part_clinique_pourcentage) }}" required style="border-color: #cbd5e1;">
                                    <span class="input-group-text bg-light text-muted">%</span>
                                    @error('part_clinique_pourcentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Quote-part conservée pour la clinique.</div>
                            </div>

                            <div class="col-md-2">
                                <label for="statut" class="form-label fw-semibold text-dark small">
                                    Statut <span class="text-danger">*</span>
                                </label>
                                <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required style="border-color: #cbd5e1;">
                                    <option value="1" {{ old('statut', $acte->statut ? '1' : '0') == '1' ? 'selected' : '' }}>Actif</option>
                                    <option value="0" {{ old('statut', $acte->statut ? '1' : '0') == '0' ? 'selected' : '' }}>Inactif</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 mb-5">
                    <a href="{{ route('actes.index') }}" class="btn btn-light border px-4 py-2">Annuler</a>
                    <button type="submit" class="btn btn-teal fw-semibold px-4 py-2 d-flex align-items-center gap-2 shadow-sm">
                        <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                        <span>Enregistrer les Modifications</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Volet latéral d'aide et prévisualisation --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4" style="border: 1px solid #e6ebf0 !important;">
                <div class="d-flex align-items-center gap-2 mb-3 text-teal">
                    <i data-lucide="help-circle" style="width: 1.25rem; height: 1.25rem;"></i>
                    <h6 class="fw-bold mb-0 text-dark">Règles & Recommandations</h6>
                </div>
                <div class="small text-muted space-y-3">
                    <p class="mb-2">
                        <strong>Historique :</strong> La modification d'un tarif d'acte n'impacte pas les tickets de caisse ou factures déjà émises dans le passé.
                    </p>
                    <p class="mb-2">
                        <strong>Désactivation :</strong> Si un acte n'est plus pratiqué, passez simplement son statut à <em>Inactif</em> plutôt que de le supprimer pour conserver la cohérence des rapports.
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 bg-white p-4" style="border: 1px solid #e6ebf0 !important; background: #f8fafc !important;">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="calculator" style="width: 1.1rem; height: 1.1rem;" class="text-teal"></i>
                    <span>Simulation en Temps Réel</span>
                </h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Tarif sélectionné :</span>
                    <span class="fw-bold font-mono text-dark" id="simTarif">0 FCFA</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Honoraires Médecin :</span>
                    <span class="fw-bold font-mono text-primary" id="simMedecin">0 FCFA</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Revenus Clinique :</span>
                    <span class="fw-bold font-mono text-success" id="simClinique">0 FCFA</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const medecinInput = document.getElementById('part_medecin_pourcentage');
        const cliniqueInput = document.getElementById('part_clinique_pourcentage');
        const tarifInput = document.getElementById('tarif_normal');

        const simTarif = document.getElementById('simTarif');
        const simMedecin = document.getElementById('simMedecin');
        const simClinique = document.getElementById('simClinique');

        function updateSimulation() {
            const tarif = parseFloat(tarifInput.value) || 0;
            const pctMed = parseFloat(medecinInput.value) || 0;
            const pctCli = parseFloat(cliniqueInput.value) || 0;

            const partMed = Math.round(tarif * (pctMed / 100));
            const partCli = Math.round(tarif * (pctCli / 100));

            simTarif.textContent = tarif.toLocaleString('fr-FR') + ' FCFA';
            simMedecin.textContent = partMed.toLocaleString('fr-FR') + ' FCFA (' + pctMed + '%)';
            simClinique.textContent = partCli.toLocaleString('fr-FR') + ' FCFA (' + pctCli + '%)';
        }

        medecinInput.addEventListener('input', function() {
            const val = parseFloat(this.value) || 0;
            if (val >= 0 && val <= 100) {
                cliniqueInput.value = (100 - val).toFixed(1);
            }
            updateSimulation();
        });

        cliniqueInput.addEventListener('input', function() {
            const val = parseFloat(this.value) || 0;
            if (val >= 0 && val <= 100) {
                medecinInput.value = (100 - val).toFixed(1);
            }
            updateSimulation();
        });

        tarifInput.addEventListener('input', updateSimulation);
        updateSimulation();
    });
</script>
@endpush
