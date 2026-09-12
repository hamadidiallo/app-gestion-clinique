@extends('layout')

@section('title', 'Nouvel Employé')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Ajouter un Nouveau Employé</h1>
            <a href="{{ route('employes.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('employes.store') }}" method="POST" class="card card-body shadow-sm">
            @csrf

            <div class="row g-3">
                {{-- Champ Nom --}}
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Prénom --}}
                <div class="col-md-6">
                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" required>
                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Téléphone --}}
                <div class="col-md-6">
                    <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                    <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}" required>
                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Email --}}
                <div class="col-md-6">
                    <label for="email" class="form-label">Adresse E-mail</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Fonction --}}
                <div class="col-md-6">
                    <label for="fonction" class="form-label">Fonction / Poste <span class="text-danger">*</span></label>
                    <input type="text" name="fonction" id="fonction" class="form-control @error('fonction') is-invalid @enderror" value="{{ old('fonction') }}" required>
                    @error('fonction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Type de Rémunération --}}
                <div class="col-md-6">
                    <label for="type_remuneration" class="form-label">Type de Rémunération <span class="text-danger">*</span></label>
                    <select name="type_remuneration" id="type_remuneration" class="form-select @error('type_remuneration') is-invalid @enderror" required>
                        <option value="fixe" {{ old('type_remuneration') == 'fixe' ? 'selected' : '' }}>Salaire Fixe</option>
                        <option value="horaire" {{ old('type_remuneration') == 'horaire' ? 'selected' : '' }}>Taux Horaire</option>
                    </select>
                    @error('type_remuneration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Salaire Fixe --}}
                <div class="col-md-4">
                    <label for="salaire_fixe" class="form-label">Salaire Fixe (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="salaire_fixe" id="salaire_fixe" class="form-control @error('salaire_fixe') is-invalid @enderror" value="{{ old('salaire_fixe') }}">
                    @error('salaire_fixe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Date d'embauche --}}
                <div class="col-md-4">
                    <label for="date_embauche" class="form-label">Date d'embauche</label>
                    <input type="date" name="date_embauche" id="date_embauche" class="form-control @error('date_embauche') is-invalid @enderror" value="{{ old('date_embauche') }}">
                    @error('date_embauche')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Statut --}}
                <div class="col-md-4">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', '1') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Champ Description --}}
                <div class="col-md-12">
                    <label for="description" class="form-label">Description / Remarques</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Enregistrer l'employé</button>
            </div>
        </form>
    </section>
@endsection
