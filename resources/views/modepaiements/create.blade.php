@extends('layout')

@section('title', 'Nouveau Mode de Paiement - CLINGEST')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary font-weight-bold mb-1">
                <i data-lucide="wallet" class="me-2"></i>Ajouter un Mode de Règlement
            </h1>
            <p class="text-muted mb-0">Configuration des moyens de paiement acceptés à la caisse de la clinique.</p>
        </div>
        <a href="{{ route('modepaiements.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i data-lucide="arrow-left" class="me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i data-lucide="gear-wide-connected" class="text-primary me-2"></i>Paramètres du Moyen de Règlement
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('modepaiements.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="nom" 
                            label="Nom du Mode de Paiement" 
                            icon="bi-cash-coin" 
                            placeholder="Ex: Espèces, Carte Bancaire, Mobile Money" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-6">
                        <x-form.input 
                            type="text" 
                            name="code" 
                            label="Code Identifiant Unique" 
                            icon="bi-qr-code" 
                            placeholder="Ex: CASH, CB, MOMO" 
                            :required="true" 
                        />
                    </div>

                    <div class="col-md-12">
                        <label for="statut" class="form-label fw-bold text-dark fs-7 mb-1 d-flex align-items-center justify-content-between">
                            <span>Statut du Mode <span class="text-danger ms-1" title="Champ obligatoire">*</span></span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i data-lucide="toggle-on"></i>
                            </span>
                            <select name="statut" id="statut" class="form-select border-start-0 @error('statut') is-invalid @enderror" required>
                                @foreach($statuts as $key => $label)
                                    <option value="{{ $key }}" {{ old('statut', '1') == (string)$key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('statut')
                            <div class="invalid-feedback d-block mt-1">
                                <i data-lucide="alert-triangle" class="me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <x-form.input 
                            type="textarea" 
                            name="description" 
                            label="Description & Remarques d'Utilisation" 
                            icon="bi-text-paragraph" 
                            placeholder="Fournir des instructions ou précisions sur l'utilisation de ce mode de paiement..." 
                            rows="3"
                        />
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm">
                        <i data-lucide="check-circle" class="me-2"></i>Enregistrer le mode de paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
