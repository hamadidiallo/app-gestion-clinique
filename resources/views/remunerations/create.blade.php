@extends('layout')

@section('title', 'Générer une Paie Médicale')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Générer une Rémunération Médicale</h1>
            <a href="{{ route('remunerations.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('remunerations.store') }}" method="POST" class="card card-body shadow-sm">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="medecin_id" class="form-label">Médecin Bénéficiaire <span class="text-danger">*</span></label>
                    <select name="medecin_id" id="medecin_id" class="form-select @error('medecin_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner un médecin --</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ old('medecin_id') == $medecin->id ? 'selected' : '' }}>
                                Dr. {{ $medecin->prenom }} {{ $medecin->nom }} ({{ $medecin->specialite }})
                            </option>
                        @endforeach
                    </select>
                    @error('medecin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="user_id" class="form-label">Agent Gestionnaire <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="type_remuneration" class="form-label">Type Rémunération <span class="text-danger">*</span></label>
                    <select name="type_remuneration" id="type_remuneration" class="form-select @error('type_remuneration') is-invalid @enderror" required>
                        <option value="pourcentage" {{ old('type_remuneration') == 'pourcentage' ? 'selected' : '' }}>Pourcentage sur actes</option>
                        <option value="fixe" {{ old('type_remuneration') == 'fixe' ? 'selected' : '' }}>Salaire fixe mensuel</option>
                        <option value="mixte" {{ old('type_remuneration') == 'mixte' ? 'selected' : '' }}>Fixe + Commission</option>
                    </select>
                    @error('type_remuneration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="periode_debut" class="form-label">Début Période <span class="text-danger">*</span></label>
                    <input type="date" name="periode_debut" id="periode_debut" class="form-control @error('periode_debut') is-invalid @enderror" value="{{ old('periode_debut', date('Y-m-01')) }}" required>
                    @error('periode_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="periode_fin" class="form-label">Fin Période <span class="text-danger">*</span></label>
                    <input type="date" name="periode_fin" id="periode_fin" class="form-control @error('periode_fin') is-invalid @enderror" value="{{ old('periode_fin', date('Y-m-t')) }}" required>
                    @error('periode_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_base" class="form-label">Montant de Base Actes (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_base" id="montant_base" class="form-control @error('montant_base') is-invalid @enderror" value="{{ old('montant_base') }}" required>
                    @error('montant_base')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_medecin" class="form-label">Part Rétrocession Médecin (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_medecin" id="montant_medecin" class="form-control @error('montant_medecin') is-invalid @enderror" value="{{ old('montant_medecin') }}" required>
                    @error('montant_medecin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_clinique" class="form-label">Part Restante Clinique (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_clinique" id="montant_clinique" class="form-control @error('montant_clinique') is-invalid @enderror" value="{{ old('montant_clinique') }}" required>
                    @error('montant_clinique')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut du Paiement <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', 'en_attente') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_paiement" class="form-label">Date Effective de Règlement</label>
                    <input type="date" name="date_paiement" id="date_paiement" class="form-control @error('date_paiement') is-invalid @enderror" value="{{ old('date_paiement') }}">
                    @error('date_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Observations / Note de paie</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Valider le Décompte</button>
            </div>
        </form>
    </section>
@endsection
