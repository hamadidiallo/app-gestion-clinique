@extends('layout')

@section('title', 'Modifier le Service Médical - ' . config('app.name'))

@section('content')
<div class="container-fluid p-0" style="max-width: 900px;">
    {{-- En-tête de la page --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="edit-3" style="width: 1.5rem; height: 1.5rem;" class="text-teal"></i>
                <span>Modifier le Service : {{ $service->nom }}</span>
            </h1>
            <p class="text-muted mb-0 small">Mise à jour des paramètres du département, code et statut opérationnel.</p>
        </div>
        <div>
            <a href="{{ route('services.index') }}" class="btn btn-light border fw-semibold d-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Retour aux services</span>
            </a>
        </div>
    </div>

    {{-- Carte du formulaire --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white" style="border: 1px solid #e6ebf0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i data-lucide="building-2" style="width: 1.15rem; height: 1.15rem;" class="text-teal"></i>
                <span>Paramètres du Service Médical</span>
            </h5>
            <span class="badge bg-light text-muted border font-mono">ID #{{ $service->id }}</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('services.update', $service) }}" method="post">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 border-0 rounded-3">
                        <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                            <i data-lucide="alert-circle" style="width: 1.1rem; height: 1.1rem;"></i>
                            <span>Veuillez corriger les erreurs suivantes :</span>
                        </div>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <x-form.input type="text" name="nom" label="Nom du Service Médical" value="{{ old('nom', $service->nom) }}" required />
                    </div>

                    <div class="col-md-4">
                        <x-form.input type="text" name="code" label="Code Identifiant" value="{{ old('code', $service->code) }}" required />
                    </div>

                    <div class="col-12">
                        <x-form.input type="text" name="description" label="Description / Missions du Service" value="{{ old('description', $service->description) }}" />
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="statut" class="form-label fw-bold small text-muted text-uppercase">Statut d'Activité : <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i data-lucide="toggle-right" style="width: 1.1rem; height: 1.1rem;"></i>
                                </span>
                                <select name="statut" id="statut" class="form-select border-start-0 @error('statut') is-invalid @enderror" required>
                                    @foreach($statuts as $key => $label)
                                        <option value="{{ $key }}" {{ old('statut', $service->statut ? '1' : '0') == (string)$key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('statut')
                                <div class="invalid-feedback d-block mt-1">
                                    <i data-lucide="alert-triangle" style="width: 0.9rem; height: 0.9rem;" class="me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2 pt-3 mt-4 border-top">
                    <a href="{{ route('services.index') }}" class="btn btn-light border fw-semibold">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-teal fw-semibold shadow-sm d-flex align-items-center gap-2">
                        <i data-lucide="save" style="width: 1.1rem; height: 1.1rem;"></i>
                        <span>Enregistrer les Modifications</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
