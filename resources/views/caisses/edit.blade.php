@extends('layout')

@section('title', 'Clôturer / Modifier la Caisse')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier / Clôturer la Caisse #{{ $caisse->id }}</h1>
            <a href="{{ route('caisses.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('caisses.update', $caisse) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="user_id" class="form-label">Caissier Responsable <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $caisse->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="date_ouverture" class="form-label">Date Ouverture <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="date_ouverture" id="date_ouverture" class="form-control @error('date_ouverture') is-invalid @enderror" value="{{ old('date_ouverture', $caisse->date_ouverture ? $caisse->date_ouverture->format('Y-m-d\TH:i') : '') }}" required>
                    @error('date_ouverture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="date_fermeture" class="form-label">Date Fermeture</label>
                    <input type="datetime-local" name="date_fermeture" id="date_fermeture" class="form-control @error('date_fermeture') is-invalid @enderror" value="{{ old('date_fermeture', $caisse->date_fermeture ? $caisse->date_fermeture->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}">
                    @error('date_fermeture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="fonds_initial" class="form-label">Fond Initial <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="fonds_initial" id="fonds_initial" class="form-control @error('fonds_initial') is-invalid @enderror" value="{{ old('fonds_initial', $caisse->fonds_initial) }}" required>
                    @error('fonds_initial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="total_entrees" class="form-label">Total Entrées (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="total_entrees" id="total_entrees" class="form-control @error('total_entrees') is-invalid @enderror" value="{{ old('total_entrees', $caisse->total_entrees) }}">
                    @error('total_entrees')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="total_sorties" class="form-label">Total Sorties (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="total_sorties" id="total_sorties" class="form-control @error('total_sorties') is-invalid @enderror" value="{{ old('total_sorties', $caisse->total_sorties) }}">
                    @error('total_sorties')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="solde_physique" class="form-label">Solde Physique (Compté)</label>
                    <input type="number" step="0.01" min="0" name="solde_physique" id="solde_physique" class="form-control @error('solde_physique') is-invalid @enderror" value="{{ old('solde_physique', $caisse->solde_physique) }}">
                    @error('solde_physique')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $caisse->statut) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="observation" class="form-label">Observations / Remarques de clôture</label>
                    <textarea name="observation" id="observation" class="form-control @error('observation') is-invalid @enderror" rows="3">{{ old('observation', $caisse->observation) }}</textarea>
                    @error('observation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer la Clôture / Modification</button>
            </div>
        </form>
    </section>
@endsection
