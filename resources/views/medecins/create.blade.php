@extends('layout')

@section('title', 'Nouveau Médecin Praticien - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête avec fil d'Ariane --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Ressources Médicales</span>
                <span class="opacity-50">/</span>
                <a href="{{ route('medecins.index') }}" class="text-decoration-none text-muted">Praticiens</a>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Nouveau Médecin</span>
            </div>
            <h1 class="h3 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                <i data-lucide="user-plus" class="lucide text-primary" style="color: var(--primary-color) !important;"></i>
                <span>Ajouter un Praticien Médical</span>
            </h1>
            <p class="text-muted small mb-0 mt-1">Enregistrement de la fiche praticien, qualification et conditions financières de rétrocession.</p>
        </div>
        <a href="{{ route('medecins.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
            <i data-lucide="arrow-left" class="lucide-sm"></i>
            <span>Retour à la liste</span>
        </a>
    </div>

    <form action="{{ route('medecins.store') }}" method="POST" id="form_medecin">
        @csrf

        <div class="row g-4">
            {{-- COLONNE GAUCHE : FORMULAIRE --}}
            <div class="col-lg-8">
                {{-- CARTE 1 : IDENTITÉ & COORDONNÉES --}}
                <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e2f1ef; color: #0f766e;">
                                <i data-lucide="user-check" class="lucide-sm"></i>
                            </span>
                            Identité & Profil Professionnel
                        </h5>
                        <span class="badge bg-light text-muted border px-2 py-1 small">Informations Requises *</span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Prénom --}}
                            <div class="col-md-6">
                                <label for="prenom" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
                                    <i data-lucide="user" class="lucide-sm text-primary"></i> Prénom <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="prenom" id="prenom" class="form-control fw-semibold @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" placeholder="Ex: Mamadou" required autofocus>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nom --}}
                            <div class="col-md-6">
                                <label for="nom" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
                                    <i data-lucide="badge-check" class="lucide-sm text-primary"></i> Nom de Famille <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nom" id="nom" class="form-control fw-semibold text-uppercase @error('nom') is-invalid @enderror" value="{{ old('nom') }}" placeholder="Ex: DIALLO" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div class="col-md-6">
                                <label for="telephone" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
                                    <i data-lucide="phone" class="lucide-sm text-primary"></i> Téléphone Professionnel <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="telephone" id="telephone" class="form-control font-mono fw-semibold @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}" placeholder="Ex: +223 76 12 34 56" required>
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Spécialité --}}
                            <div class="col-md-6">
                                <label for="specialite" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
                                    <i data-lucide="stethoscope" class="lucide-sm text-primary"></i> Spécialité Médicale <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="specialite" id="specialite" list="specialites_list" class="form-control fw-semibold @error('specialite') is-invalid @enderror" value="{{ old('specialite', 'Médecine Générale') }}" placeholder="Sélectionnez ou tapez..." required>
                                <datalist id="specialites_list">
                                    <option value="Médecine Générale">
                                    <option value="Pédiatrie">
                                    <option value="Gynécologie-Obstétrique">
                                    <option value="Cardiologie">
                                    <option value="Chirurgie Générale">
                                    <option value="Ophtalmologie">
                                    <option value="Dermatologie">
                                    <option value="Radiologie & Échographie">
                                    <option value="Odontologie / Dentisterie">
                                    <option value="Anesthésie-Réanimation">
                                </datalist>
                                @error('specialite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Raccourcis rapides de spécialité --}}
                            <div class="col-12 pt-1">
                                <span class="text-muted small me-2">Suggestions rapides :</span>
                                <div class="d-inline-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-specialite" data-val="Médecine Générale">Médecine Générale</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-specialite" data-val="Pédiatrie">Pédiatrie</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-specialite" data-val="Gynécologie-Obstétrique">Gynécologie</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-specialite" data-val="Cardiologie">Cardiologie</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small quick-specialite" data-val="Ophtalmologie">Ophtalmologie</button>
                                </div>
                            </div>

                            {{-- Statut d'activité --}}
                            <div class="col-12 pt-2">
                                <label class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
                                    <i data-lucide="power" class="lucide-sm text-primary"></i> Statut d'Exercice au Sein de la Clinique
                                </label>
                                <div class="d-flex gap-3">
                                    <label class="d-flex align-items-center gap-2 p-2 px-3 border rounded-3 bg-white cursor-pointer shadow-sm">
                                        <input type="radio" name="statut" value="1" class="form-check-input mt-0" {{ old('statut', '1') == '1' ? 'checked' : '' }}>
                                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">🟢 Actif (En exercice)</span>
                                    </label>
                                    <label class="d-flex align-items-center gap-2 p-2 px-3 border rounded-3 bg-white cursor-pointer shadow-sm">
                                        <input type="radio" name="statut" value="0" class="form-check-input mt-0" {{ old('statut') == '0' ? 'checked' : '' }}>
                                        <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1">⚪ Inactif (Congé / Suspendu)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARTE 2 : MODÈLE FINANCIER & RÉTROCESSION --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <span class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e0f2fe; color: #0284c7;">
                                <i data-lucide="wallet" class="lucide-sm"></i>
                            </span>
                            Rémunération & Rétrocession d'Honoraires
                        </h5>
                        <span class="badge bg-primary-subtle text-primary border-0 px-2 py-1 small fw-bold">Devise : FCFA</span>
                    </div>

                    <div class="card-body p-4">
                        {{-- Sélection visuelle du mode de rémunération --}}
                        <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                            <span>Type de Contrat de Rémunération</span> <span class="text-danger">*</span>
                        </label>

                        <div class="row g-3 mb-4">
                            {{-- Option 1 : Pourcentage --}}
                            <div class="col-md-4">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer remuneration-card transition-all" id="card_pourcentage">
                                    <div class="d-flex align-items-start gap-2">
                                        <input type="radio" name="type_remuneration" value="pourcentage" class="form-check-input mt-1" {{ old('type_remuneration', 'pourcentage') == 'pourcentage' ? 'checked' : '' }}>
                                        <div>
                                            <strong class="d-block text-dark small">À l'Acte (Pourcentage)</strong>
                                            <span class="text-muted extra-small d-block mt-1">Rétrocession proportionnelle aux prestations facturées.</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Option 2 : Salaire Fixe --}}
                            <div class="col-md-4">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer remuneration-card transition-all" id="card_fixe">
                                    <div class="d-flex align-items-start gap-2">
                                        <input type="radio" name="type_remuneration" value="fixe" class="form-check-input mt-1" {{ old('type_remuneration') == 'fixe' ? 'checked' : '' }}>
                                        <div>
                                            <strong class="d-block text-dark small">Salaire Fixe Mensuel</strong>
                                            <span class="text-muted extra-small d-block mt-1">Montant mensuel forfaitaire garanti sans rétrocession.</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Option 3 : Mixte --}}
                            <div class="col-md-4">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer remuneration-card transition-all" id="card_mixte">
                                    <div class="d-flex align-items-start gap-2">
                                        <input type="radio" name="type_remuneration" value="mixte" class="form-check-input mt-1" {{ old('type_remuneration') == 'mixte' ? 'checked' : '' }}>
                                        <div>
                                            <strong class="d-block text-dark small">Mixte (Fixe + Part)</strong>
                                            <span class="text-muted extra-small d-block mt-1">Salaire de base fixe + pourcentage sur les actes.</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Champs de montants paramétrables --}}
                        <div class="row g-3 p-3 rounded-3 bg-light border">
                            {{-- Pourcentage --}}
                            <div class="col-md-6" id="wrapper_pourcentage">
                                <label for="pourcentage" class="form-label fw-bold text-dark small d-flex align-items-center justify-content-between">
                                    <span>Taux de Rétrocession Médecin</span>
                                    <span class="text-primary font-mono fw-bold" id="badge_pourcentage_preview">40%</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.5" min="0" max="100" name="pourcentage" id="pourcentage" class="form-control font-mono fw-bold text-end @error('pourcentage') is-invalid @enderror" value="{{ old('pourcentage', '40') }}" placeholder="40">
                                    <span class="input-group-text bg-white fw-bold">%</span>
                                </div>
                                <div class="form-text extra-small text-muted mt-1">
                                    Part des honoraires reversée au praticien sur chaque acte médical.
                                </div>
                                @error('pourcentage')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Base Fixe Mensuelle --}}
                            <div class="col-md-6" id="wrapper_salaire_fixe">
                                <label for="salaire_fixe" class="form-label fw-bold text-dark small d-flex align-items-center justify-content-between">
                                    <span>Salaire Fixe Mensuel</span>
                                    <span class="text-muted small">Optionnel</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" name="salaire_fixe" id="salaire_fixe" class="form-control font-mono fw-bold text-end @error('salaire_fixe') is-invalid @enderror" value="{{ old('salaire_fixe', '0') }}" placeholder="0">
                                    <span class="input-group-text bg-white fw-bold font-mono">FCFA</span>
                                </div>
                                <div class="form-text extra-small text-muted mt-1">
                                    Traitement mensuel fixe versé indépendamment du volume d'actes.
                                </div>
                                @error('salaire_fixe')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Soumission --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('medecins.index') }}" class="btn btn-light border px-4">Annuler</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                        <i data-lucide="check-circle" class="lucide-sm"></i>
                        <span>Enregistrer le Praticien</span>
                    </button>
                </div>
            </div>

            {{-- COLONNE DROITE : APERÇU EN DIRECT & SIMULATION --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px;">
                    {{-- CARTE APERÇU PRATICIEN --}}
                    <div class="card shadow-sm border-0 rounded-4 mb-4 text-center p-4 bg-white">
                        <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" id="avatar_preview" style="width: 76px; height: 76px; font-size: 1.6rem; background: linear-gradient(135deg, #0f766e, #14b8a6);">
                            MD
                        </div>
                        <h5 class="fw-bold text-dark mb-1" id="name_preview">Dr Mamadou DIALLO</h5>
                        <p class="text-muted small mb-2 d-flex align-items-center justify-content-center gap-1">
                            <i data-lucide="stethoscope" class="lucide-sm text-primary"></i>
                            <span id="specialite_preview">Médecine Générale</span>
                        </p>
                        <div>
                            <span class="badge bg-success-subtle text-success px-3 py-1 fw-bold rounded-pill" id="badge_status_preview">Actif</span>
                        </div>

                        <hr class="my-3 opacity-50">

                        <div class="text-start small">
                            <div class="d-flex justify-content-between text-muted mb-1">
                                <span>Contrat :</span>
                                <strong class="text-dark" id="contrat_preview">Pourcentage</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted mb-1">
                                <span>Téléphone :</span>
                                <strong class="text-dark font-mono" id="phone_preview">--</strong>
                            </div>
                        </div>
                    </div>

                    {{-- CARTE SIMULATEUR DE RÉTROCESSION --}}
                    <div class="card shadow-sm border-0 rounded-4 p-4" style="background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i data-lucide="calculator" class="lucide-sm text-primary"></i>
                            <span>Simulation Rétrocession (Base 100 000 FCFA)</span>
                        </h6>
                        <p class="text-muted extra-small mb-3">Exemple de répartition pour 100 000 FCFA de soins ou consultations réalisés par le praticien :</p>

                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded-3 bg-white border">
                            <div class="small">
                                <span class="fw-bold text-success d-block">Honoraires Praticien</span>
                                <span class="extra-small text-muted" id="sim_medecin_rate">Taux : 40%</span>
                            </div>
                            <strong class="font-mono fs-6 text-success" id="sim_medecin_amount">40 000 FCFA</strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-white border">
                            <div class="small">
                                <span class="fw-bold text-primary d-block">Part Clinique</span>
                                <span class="extra-small text-muted" id="sim_clinique_rate">Taux : 60%</span>
                            </div>
                            <strong class="font-mono fs-6 text-primary" id="sim_clinique_amount">60 000 FCFA</strong>
                        </div>

                        <div class="mt-3 p-2 rounded-3 bg-light extra-small text-muted" id="sim_fixe_note">
                            <i data-lucide="info" class="lucide-xs me-1"></i> Rétrocession pure sans base fixe mensuelle.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputPrenom = document.getElementById('prenom');
    const inputNom = document.getElementById('nom');
    const inputTel = document.getElementById('telephone');
    const inputSpecialite = document.getElementById('specialite');
    const inputPourcentage = document.getElementById('pourcentage');
    const inputSalaireFixe = document.getElementById('salaire_fixe');

    const namePreview = document.getElementById('name_preview');
    const avatarPreview = document.getElementById('avatar_preview');
    const specialitePreview = document.getElementById('specialite_preview');
    const phonePreview = document.getElementById('phone_preview');
    const contratPreview = document.getElementById('contrat_preview');

    const simMedecinRate = document.getElementById('sim_medecin_rate');
    const simMedecinAmount = document.getElementById('sim_medecin_amount');
    const simCliniqueRate = document.getElementById('sim_clinique_rate');
    const simCliniqueAmount = document.getElementById('sim_clinique_amount');
    const simFixeNote = document.getElementById('sim_fixe_note');
    const badgePourcentagePreview = document.getElementById('badge_pourcentage_preview');

    const wrapperPourcentage = document.getElementById('wrapper_pourcentage');
    const wrapperSalaireFixe = document.getElementById('wrapper_salaire_fixe');

    function updateLivePreview() {
        const prenom = (inputPrenom.value || '').trim();
        const nom = (inputNom.value || '').trim();
        const tel = (inputTel.value || '').trim();
        const spec = (inputSpecialite.value || '').trim() || 'Médecine Générale';

        // Nom complet & Avatar initiales
        if (prenom || nom) {
            namePreview.textContent = 'Dr ' + prenom + ' ' + nom.toUpperCase();
            const initP = prenom ? prenom.charAt(0).toUpperCase() : '';
            const initN = nom ? nom.charAt(0).toUpperCase() : '';
            avatarPreview.textContent = (initP + initN) || 'MD';
        } else {
            namePreview.textContent = 'Dr Mamadou DIALLO';
            avatarPreview.textContent = 'MD';
        }

        specialitePreview.textContent = spec;
        phonePreview.textContent = tel || '--';

        // Type rémunération
        const selectedRadio = document.querySelector('input[name="type_remuneration"]:checked');
        const type = selectedRadio ? selectedRadio.value : 'pourcentage';

        document.querySelectorAll('.remuneration-card').forEach(c => {
            c.classList.remove('border-primary', 'bg-primary-subtle');
        });
        const activeCard = document.getElementById('card_' + type);
        if (activeCard) {
            activeCard.classList.add('border-primary');
        }

        const pct = Math.max(0, Math.min(100, parseFloat(inputPourcentage.value) || 0));
        badgePourcentagePreview.textContent = pct + '%';
        const fixe = Math.max(0, parseFloat(inputSalaireFixe.value) || 0);

        if (type === 'pourcentage') {
            contratPreview.textContent = 'À l\'Acte (' + pct + '%)';
            wrapperPourcentage.style.opacity = '1';
            wrapperSalaireFixe.style.opacity = '0.4';
            
            const partMedecin = Math.round(100000 * (pct / 100));
            const partClinique = 100000 - partMedecin;

            simMedecinRate.textContent = 'Taux : ' + pct + '%';
            simMedecinAmount.textContent = new Intl.NumberFormat('fr-FR').format(partMedecin) + ' FCFA';
            simCliniqueRate.textContent = 'Taux : ' + (100 - pct) + '%';
            simCliniqueAmount.textContent = new Intl.NumberFormat('fr-FR').format(partClinique) + ' FCFA';
            simFixeNote.innerHTML = '<i data-lucide="check" class="lucide-xs me-1"></i> Rétrocession pure : proportionnelle aux actes générés.';
        } else if (type === 'fixe') {
            contratPreview.textContent = 'Salaire Fixe';
            wrapperPourcentage.style.opacity = '0.4';
            wrapperSalaireFixe.style.opacity = '1';

            simMedecinRate.textContent = 'Fixe garanti';
            simMedecinAmount.textContent = new Intl.NumberFormat('fr-FR').format(fixe) + ' FCFA/mois';
            simCliniqueRate.textContent = 'Recettes actes';
            simCliniqueAmount.textContent = '100% Clinique';
            simFixeNote.innerHTML = '<i data-lucide="info" class="lucide-xs me-1"></i> Médecin rémunéré par salaire mensuel fixe indépendant des actes.';
        } else {
            contratPreview.textContent = 'Mixte (' + pct + '% + Fixe)';
            wrapperPourcentage.style.opacity = '1';
            wrapperSalaireFixe.style.opacity = '1';

            const partMedecin = Math.round(100000 * (pct / 100));
            const partClinique = 100000 - partMedecin;

            simMedecinRate.textContent = pct + '% + Fixe';
            simMedecinAmount.textContent = new Intl.NumberFormat('fr-FR').format(partMedecin) + ' FCFA + Base';
            simCliniqueRate.textContent = 'Taux : ' + (100 - pct) + '%';
            simCliniqueAmount.textContent = new Intl.NumberFormat('fr-FR').format(partClinique) + ' FCFA';
            simFixeNote.innerHTML = '<i data-lucide="layers" class="lucide-xs me-1"></i> Base mensuelle de ' + new Intl.NumberFormat('fr-FR').format(fixe) + ' FCFA + ' + pct + '% des actes.';
        }

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    // Écouteurs de saisie en temps réel
    inputPrenom.addEventListener('input', updateLivePreview);
    inputNom.addEventListener('input', updateLivePreview);
    inputTel.addEventListener('input', updateLivePreview);
    inputSpecialite.addEventListener('input', updateLivePreview);
    inputPourcentage.addEventListener('input', updateLivePreview);
    inputSalaireFixe.addEventListener('input', updateLivePreview);

    document.querySelectorAll('input[name="type_remuneration"]').forEach(radio => {
        radio.addEventListener('change', updateLivePreview);
    });

    document.querySelectorAll('input[name="statut"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const badge = document.getElementById('badge_status_preview');
            if (this.value === '1') {
                badge.className = 'badge bg-success-subtle text-success px-3 py-1 fw-bold rounded-pill';
                badge.textContent = 'Actif';
            } else {
                badge.className = 'badge bg-secondary-subtle text-secondary px-3 py-1 fw-bold rounded-pill';
                badge.textContent = 'Inactif';
            }
        });
    });

    // Raccourcis de spécialité
    document.querySelectorAll('.quick-specialite').forEach(btn => {
        btn.addEventListener('click', function() {
            inputSpecialite.value = this.dataset.val;
            updateLivePreview();
        });
    });

    // Initialisation
    updateLivePreview();
});
</script>
<style>
.cursor-pointer { cursor: pointer; }
.extra-small { font-size: 0.78rem; }
.transition-all { transition: all 0.2s ease-in-out; }
.remuneration-card:hover { border-color: var(--primary-color) !important; background-color: #f0fdfa; }
</style>
@endpush
