@extends('layout')

@section('title', 'Modifier la Paie Médicale')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier le Décompte #{{ $remuneration->id }}</h1>
            <a href="{{ route('remunerations.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('remunerations.update', $remuneration) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="medecin_id" class="form-label">Médecin <span class="text-danger">*</span></label>
                    <select name="medecin_id" id="medecin_id" class="form-select @error('medecin_id') is-invalid @enderror" required>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ old('medecin_id', $remuneration->medecin_id) == $medecin->id ? 'selected' : '' }}>
                                Dr. {{ $medecin->prenom }} {{ $medecin->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('medecin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="user_id" class="form-label">Agent <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $remuneration->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="type_remuneration" class="form-label">Type Rémunération <span class="text-danger">*</span></label>
                    <select name="type_remuneration" id="type_remuneration" class="form-select @error('type_remuneration') is-invalid @enderror" required>
                        <option value="pourcentage" {{ old('type_remuneration', $remuneration->type_remuneration) == 'pourcentage' ? 'selected' : '' }}>Pourcentage</option>
                        <option value="fixe" {{ old('type_remuneration', $remuneration->type_remuneration) == 'fixe' ? 'selected' : '' }}>Fixe</option>
                        <option value="mixte" {{ old('type_remuneration', $remuneration->type_remuneration) == 'mixte' ? 'selected' : '' }}>Mixte</option>
                    </select>
                    @error('type_remuneration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="periode_debut" class="form-label">Début Période <span class="text-danger">*</span></label>
                    <input type="date" name="periode_debut" id="periode_debut" class="form-control @error('periode_debut') is-invalid @enderror" value="{{ old('periode_debut', $remuneration->periode_debut ? $remuneration->periode_debut->format('Y-m-d') : '') }}" required>
                    @error('periode_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="periode_fin" class="form-label">Fin Période <span class="text-danger">*</span></label>
                    <input type="date" name="periode_fin" id="periode_fin" class="form-control @error('periode_fin') is-invalid @enderror" value="{{ old('periode_fin', $remuneration->periode_fin ? $remuneration->periode_fin->format('Y-m-d') : '') }}" required>
                    @error('periode_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_base" class="form-label">Montant de Base <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_base" id="montant_base" class="form-control @error('montant_base') is-invalid @enderror" value="{{ old('montant_base', $remuneration->montant_base) }}" required>
                    @error('montant_base')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_medecin" class="form-label">Montant Médecin <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_medecin" id="montant_medecin" class="form-control @error('montant_medecin') is-invalid @enderror" value="{{ old('montant_medecin', $remuneration->montant_medecin) }}" required>
                    @error('montant_medecin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_clinique" class="form-label">Montant Clinique <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_clinique" id="montant_clinique" class="form-control @error('montant_clinique') is-invalid @enderror" value="{{ old('montant_clinique', $remuneration->montant_clinique) }}" required>
                    @error('montant_clinique')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $remuneration->statut) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_paiement" class="form-label">Date Règlement</label>
                    <input type="date" name="date_paiement" id="date_paiement" class="form-control @error('date_paiement') is-invalid @enderror" value="{{ old('date_paiement', $remuneration->date_paiement ? $remuneration->date_paiement->format('Y-m-d') : '') }}">
                    @error('date_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Observations</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $remuneration->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
