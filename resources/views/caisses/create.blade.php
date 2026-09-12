@extends('layout')

@section('title', 'Ouvrir une Caisse')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Ouvrir une Nouvelle Session de Caisse</h1>
            <a href="{{ route('caisses.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('caisses.store') }}" method="POST" class="card card-body shadow-sm">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="user_id" class="form-label">Caissier Responsable <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Choisir un caissier --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_ouverture" class="form-label">Date & Heure d'Ouverture <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="date_ouverture" id="date_ouverture" class="form-control @error('date_ouverture') is-invalid @enderror" value="{{ old('date_ouverture', date('Y-m-d\TH:i')) }}" required>
                    @error('date_ouverture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="fonds_initial" class="form-label">Fond de Caisse Initial (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="fonds_initial" id="fonds_initial" class="form-control @error('fonds_initial') is-invalid @enderror" value="{{ old('fonds_initial', 0) }}" required>
                    @error('fonds_initial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="total_entrees" class="form-label">Total Entrées (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="total_entrees" id="total_entrees" class="form-control @error('total_entrees') is-invalid @enderror" value="{{ old('total_entrees', 0) }}">
                    @error('total_entrees')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="total_sorties" class="form-label">Total Sorties (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="total_sorties" id="total_sorties" class="form-control @error('total_sorties') is-invalid @enderror" value="{{ old('total_sorties', 0) }}">
                    @error('total_sorties')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut Initial <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', 'ouverte') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="observation" class="form-label">Observations / Remarques</label>
                    <textarea name="observation" id="observation" class="form-control @error('observation') is-invalid @enderror" rows="3">{{ old('observation') }}</textarea>
                    @error('observation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Ouvrir la Caisse</button>
            </div>
        </form>
    </section>
@endsection
