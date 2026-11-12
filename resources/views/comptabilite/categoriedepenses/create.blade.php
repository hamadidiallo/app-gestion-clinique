@extends('layout')

@section('title', 'Nouvelle Catégorie de Dépense')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Créer une Catégorie de Dépense</h1>
            <a href="{{ route('categoriedepenses.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('categoriedepenses.store') }}" method="POST" class="card card-body shadow-sm">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom de la Catégorie <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" placeholder="Ex: Électricité, Fournitures, Loyer" required>
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="code" class="form-label">Code Identifiant <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Ex: DEP-ELEC, DEP-FOURN" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', '1') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Enregistrer la catégorie</button>
            </div>
        </form>
    </section>
@endsection
