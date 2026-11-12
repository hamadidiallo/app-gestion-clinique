@extends('layout')

@section('title', 'Modifier le Rôle : ' . $role->nom . ' - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête de page & Navigation --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('roles.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Retour aux Rôles
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small font-mono">Édition Rôle</span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <div class="bg-primary-subtle text-primary p-2 rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i data-lucide="shield-check" class="lucide"></i>
                </div>
                <span>Modifier le Rôle : {{ $role->nom }}</span>
            </h1>
            <p class="text-muted mb-0 small">
                Mettez à jour le nom officiel ou la description des prérogatives de ce profil utilisateur.
            </p>
        </div>

        <div class="d-flex gap-2 no-print">
            <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 shadow-sm">
                <i data-lucide="eye" class="lucide-sm"></i>
                <span>Voir la Fiche</span>
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2 px-3">
                <i data-lucide="x" class="lucide-sm"></i>
                <span>Annuler</span>
            </a>
        </div>
    </div>

    {{-- Formulaire de modification dans une carte centrée --}}
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <form action="{{ route('roles.update', $role) }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-1">Détails du Profil</h5>
                        <p class="text-muted small mb-0">Modifier les informations associées à ce rôle d'accès.</p>
                    </div>

                    {{-- Nom du rôle --}}
                    <div class="mb-3">
                        <label for="nom" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                            <i data-lucide="shield" class="lucide-sm text-muted"></i>
                            <span>Intitulé du Rôle <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $role->nom) }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description du rôle --}}
                    <div class="mb-4">
                        <label for="description" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                            <i data-lucide="file-text" class="lucide-sm text-muted"></i>
                            <span>Description &amp; Périmètre d'Action</span>
                        </label>
                        <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('roles.index') }}" class="btn btn-light px-3">Annuler</a>
                        <button type="submit" class="btn btn-primary fw-bold d-inline-flex align-items-center gap-2 px-4 shadow-sm">
                            <i data-lucide="check" class="lucide-sm"></i>
                            <span>Mettre à jour le Rôle</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
