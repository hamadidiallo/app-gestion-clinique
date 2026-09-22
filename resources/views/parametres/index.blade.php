@extends('layout')

@section('title', 'Paramètres & Configuration de la Clinique - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">

    {{-- Fil d'Ariane et En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                <span>Administration</span>
                <span class="opacity-50">/</span>
                <span class="fw-semibold text-dark">Configuration Système</span>
            </div>
            <h1 class="h3 fw-bold mb-0 text-dark">Paramètres de la Clinique</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('abonnement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="sparkles" class="lucide-sm text-teal"></i>
                <span>Gérer l'Abonnement</span>
            </a>
            <button type="submit" form="clinicSettingsForm" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i data-lucide="save" class="lucide-sm"></i>
                <span class="fw-semibold">Enregistrer les Modifications</span>
            </button>
        </div>
    </div>

    {{-- Messages d'alerte et erreurs --}}
    <x-form.erreur />
    <x-form.alert />

    <form method="POST" action="{{ route('parametres.update') }}" enctype="multipart/form-data" id="clinicSettingsForm">
        @csrf
        @method('PUT')

        <div class="row g-4 mb-5">
            {{-- Colonne Gauche : Logo & Identité Visuelle --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="image" class="lucide-sm text-teal"></i>
                            <span>Logo Officiel de l'Établissement</span>
                        </h2>
                    </div>
                    <div class="card-body p-4 text-center">
                        {{-- Aperçu du logo actuel ou placeholder --}}
                        <div class="mb-3 d-flex justify-content-center">
                            <div id="logoPreviewContainer" class="rounded-3 border d-flex align-items-center justify-content-center overflow-hidden position-relative shadow-sm"
                                 style="width: 140px; height: 140px; background: #f8fafc; border-color: #cbd5e1 !important;">
                                @if($clinique && $clinique->logo)
                                    <img src="{{ asset('storage/' . $clinique->logo) }}" alt="Logo Clinique" id="logoPreview" style="width: 100%; height: 100%; object-fit: contain; padding: 6px;">
                                @else
                                    <img src="" alt="Aperçu" id="logoPreview" style="width: 100%; height: 100%; object-fit: contain; padding: 6px; display: none;">
                                    <div id="logoPlaceholder" class="text-center p-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 46px; height: 46px; background: #e2f1ef; color: #0f766e;">
                                            <i data-lucide="cross" style="width: 24px; height: 24px;"></i>
                                        </div>
                                        <span class="text-muted small d-block" style="font-size: 0.72rem;">Aucun logo</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Sélecteur de fichier --}}
                        <div class="mb-3">
                            <label for="logoInput" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2 mb-2">
                                <i data-lucide="upload" class="lucide-xs"></i>
                                <span>Changer le logo</span>
                            </label>
                            <input type="file" name="logo" id="logoInput" class="d-none" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp" onchange="previewImage(this)">
                            <div class="text-muted small" style="font-size: 0.72rem;">PNG, JPG, SVG ou WebP (max 2 Mo).</div>
                        </div>

                        @if($clinique && $clinique->logo)
                            <div class="form-check text-start p-2 bg-light rounded-3 border small">
                                <input class="form-check-input" type="checkbox" name="supprimer_logo" id="supprimer_logo" value="1">
                                <label class="form-check-label text-danger fw-semibold" for="supprimer_logo">
                                    Supprimer le logo actuel
                                </label>
                            </div>
                        @endif

                        <div class="alert alert-light border mt-3 mb-0 text-start small p-3 text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                            <i data-lucide="info" class="lucide-xs text-teal me-1"></i>
                            Ce logo apparaîtra automatiquement sur vos <strong>tickets thermiques</strong>, <strong>reçus de paiement</strong>, <strong>factures A4</strong> et <strong>bordereaux d'assurance</strong>.
                        </div>
                    </div>
                </div>

                {{-- Carte Code d'Invitation Rapide --}}
                <div class="card border-0 shadow-sm" style="border-radius: 14px; background: linear-gradient(135deg, #092c28 0%, #0f766e 100%); color: #ffffff;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-2 text-white-50 small">
                            <i data-lucide="key" class="lucide-sm text-teal-subtle" style="color: #5eead4;"></i>
                            <span class="fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Invitation des Collaborateurs</span>
                        </div>
                        <div class="font-mono fs-4 fw-bold mb-2 text-white letter-spacing-1" id="invitationCodeText">
                            {{ $clinique->code_invitation ?? 'NON-DÉFINI' }}
                        </div>
                        <p class="text-white-50 small mb-3">
                            Transmettez ce code à vos médecins et caissiers lors de leur inscription sur la plateforme.
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-light fw-bold text-dark d-inline-flex align-items-center gap-1" onclick="copyInvitationCode()">
                                <i data-lucide="copy" class="lucide-xs"></i>
                                <span id="copyText">Copier le code</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#regenerateCodeModal">
                                <i data-lucide="refresh-cw" class="lucide-xs"></i>
                                <span>Régénérer</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne Droite : Formulaire des Informations & Facturation --}}
            <div class="col-lg-8">
                {{-- Informations Générales --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="building-2" class="lucide-sm text-teal"></i>
                            <span>1. Raison Sociale & Établissement</span>
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nom" class="form-label fw-semibold text-dark small">Nom Officiel de la Clinique <span class="text-danger">*</span></label>
                                <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $clinique->nom ?? '') }}" required placeholder="Ex: Clinique Médico-Chirurgicale Espoir">
                                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="type_etablissement" class="form-label fw-semibold text-dark small">Type d'Établissement</label>
                                <select name="type_etablissement" id="type_etablissement" class="form-select @error('type_etablissement') is-invalid @enderror">
                                    <option value="Policlinique" {{ old('type_etablissement', $clinique->type_etablissement ?? '') == 'Policlinique' ? 'selected' : '' }}>Policlinique</option>
                                    <option value="Clinique" {{ old('type_etablissement', $clinique->type_etablissement ?? '') == 'Clinique' ? 'selected' : '' }}>Clinique Médico-Chirurgicale</option>
                                    <option value="Cabinet" {{ old('type_etablissement', $clinique->type_etablissement ?? '') == 'Cabinet' ? 'selected' : '' }}>Cabinet Médical</option>
                                    <option value="Centre de santé" {{ old('type_etablissement', $clinique->type_etablissement ?? '') == 'Centre de santé' ? 'selected' : '' }}>Centre de Santé / Dispensaire</option>
                                    <option value="Hôpital" {{ old('type_etablissement', $clinique->type_etablissement ?? '') == 'Hôpital' ? 'selected' : '' }}>Hôpital Privé</option>
                                </select>
                                @error('type_etablissement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telephone" class="form-label fw-semibold text-dark small">Standard Téléphonique</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i data-lucide="phone" class="lucide-xs"></i></span>
                                    <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', $clinique->telephone ?? '') }}" placeholder="Ex: (+223) 20 22 00 00">
                                </div>
                                @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-dark small">Adresse E-mail Officielle</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i data-lucide="mail" class="lucide-xs"></i></span>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $clinique->email ?? '') }}" placeholder="contact@clinique-espoir.ml">
                                </div>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="adresse" class="form-label fw-semibold text-dark small">Adresse Géographique (Quartier, Rue, Porte)</label>
                                <input type="text" name="adresse" id="adresse" class="form-control @error('adresse') is-invalid @enderror" value="{{ old('adresse', $clinique->adresse ?? '') }}" placeholder="Ex: Hamdallaye ACI 2000, Rue 310, Porte 45">
                                @error('adresse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="ville" class="form-label fw-semibold text-dark small">Ville <span class="text-danger">*</span></label>
                                <input type="text" name="ville" id="ville" class="form-control @error('ville') is-invalid @enderror" value="{{ old('ville', $clinique->ville ?? 'Bamako') }}" required placeholder="Ex: Bamako">
                                @error('ville')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="pays" class="form-label fw-semibold text-dark small">Pays <span class="text-danger">*</span></label>
                                <input type="text" name="pays" id="pays" class="form-control @error('pays') is-invalid @enderror" value="{{ old('pays', $clinique->pays ?? 'Mali') }}" required placeholder="Ex: Mali">
                                @error('pays')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Paramètres de Facturation & Numérotation --}}
                <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="receipt" class="lucide-sm text-teal"></i>
                            <span>2. Facturation, Monnaie & Numérotation</span>
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="devise" class="form-label fw-semibold text-dark small">Devise Monétaire <span class="text-danger">*</span></label>
                                <input type="text" name="devise" id="devise" class="form-control font-mono fw-bold @error('devise') is-invalid @enderror" value="{{ old('devise', $clinique->devise ?? 'FCFA') }}" required placeholder="FCFA">
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Ex : FCFA, EUR, USD.</div>
                                @error('devise')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="prefixe_ticket" class="form-label fw-semibold text-dark small">Préfixe Tickets <span class="text-danger">*</span></label>
                                <input type="text" name="prefixe_ticket" id="prefixe_ticket" class="form-control font-mono fw-bold text-uppercase @error('prefixe_ticket') is-invalid @enderror" value="{{ old('prefixe_ticket', $clinique->prefixe_ticket ?? 'TCK') }}" required placeholder="TCK" maxlength="10">
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Génère : <code>TCK-0001</code></div>
                                @error('prefixe_ticket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="prefixe_patient" class="form-label fw-semibold text-dark small">Préfixe Patients <span class="text-danger">*</span></label>
                                <input type="text" name="prefixe_patient" id="prefixe_patient" class="form-control font-mono fw-bold text-uppercase @error('prefixe_patient') is-invalid @enderror" value="{{ old('prefixe_patient', $clinique->prefixe_patient ?? 'PAT') }}" required placeholder="PAT" maxlength="10">
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Génère : <code>PAT-0001</code></div>
                                @error('prefixe_patient')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 fw-bold shadow-sm">
                                <i data-lucide="check" class="lucide-sm"></i>
                                <span>Enregistrer les Paramètres</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

{{-- MODAL DE CONFIRMATION DE RÉGÉNÉRATION DU CODE --}}
<div class="modal fade" id="regenerateCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="alert-triangle" class="lucide-sm"></i>
                    <span>Régénérer le Code d'Invitation</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <p class="mb-3">
                    Êtes-vous sûr de vouloir générer un nouveau code d'invitation pour <strong>{{ $clinique->nom ?? 'votre clinique' }}</strong> ?
                </p>
                <div class="alert alert-warning small mb-0">
                    L'ancien code (<code>{{ $clinique->code_invitation ?? 'N/A' }}</code>) deviendra immédiatement invalide. Vos futurs collaborateurs devront utiliser le nouveau code pour s'inscrire.
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="{{ route('parametres.regenerer-code') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning fw-bold">Confirmer et Régénérer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('logoPreview');
                const placeholder = document.getElementById('logoPlaceholder');
                img.src = e.target.result;
                img.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function copyInvitationCode() {
        const codeElement = document.getElementById('invitationCodeText');
        if (!codeElement) return;
        const code = codeElement.innerText.trim();

        navigator.clipboard.writeText(code).then(() => {
            const copyText = document.getElementById('copyText');
            if (copyText) {
                const originalText = copyText.innerText;
                copyText.innerText = 'Copié !';
                setTimeout(() => {
                    copyText.innerText = originalText;
                }, 2000);
            }
        }).catch(err => {
            console.error('Erreur lors de la copie :', err);
        });
    }
</script>
@endpush
@endsection
