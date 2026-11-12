@extends('layout')

@section('title', 'Nouvelle Règle de Partage')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Créer une Règle de Partage d'Honoraires</h1>
            <a href="{{ route('reglespartage.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('reglespartage.store') }}" method="POST" class="card card-body shadow-sm">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="service_id" class="form-label">Service Médical <span class="text-danger">*</span></label>
                    <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                        <option value="">-- Choisir un service --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }} ({{ $service->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="pourcentage_medecin" class="form-label">Pourcentage Médecin (%) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" max="100" name="pourcentage_medecin" id="pourcentage_medecin" class="form-control @error('pourcentage_medecin') is-invalid @enderror" value="{{ old('pourcentage_medecin', 50) }}" required>
                    @error('pourcentage_medecin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="pourcentage_clinique" class="form-label">Pourcentage Clinique (%) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" max="100" name="pourcentage_clinique" id="pourcentage_clinique" class="form-control @error('pourcentage_clinique') is-invalid @enderror" value="{{ old('pourcentage_clinique', 50) }}" required>
                    @error('pourcentage_clinique')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="date_debut" class="form-label">Date de Début <span class="text-danger">*</span></label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control @error('date_debut') is-invalid @enderror" value="{{ old('date_debut', date('Y-m-d')) }}" required>
                    @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="date_fin" class="form-label">Date de Fin (Optionnel)</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control @error('date_fin') is-invalid @enderror" value="{{ old('date_fin') }}">
                    @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', '1') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Description / Remarques</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Enregistrer la Règle</button>
            </div>
        </form>
    </section>
@endsection
