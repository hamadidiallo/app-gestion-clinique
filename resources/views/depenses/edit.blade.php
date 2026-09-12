@extends('layout')

@section('title', 'Modifier la Dépense')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier la Dépense : {{ $depense->reference }}</h1>
            <a href="{{ route('depenses.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('depenses.update', $depense) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $depense->reference) }}" required>
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="categorie_depense_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                    <select name="categorie_depense_id" id="categorie_depense_id" class="form-select @error('categorie_depense_id') is-invalid @enderror" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('categorie_depense_id', $depense->categorie_depense_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_depense_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="beneficiaire" class="form-label">Bénéficiaire <span class="text-danger">*</span></label>
                    <input type="text" name="beneficiaire" id="beneficiaire" class="form-control @error('beneficiaire') is-invalid @enderror" value="{{ old('beneficiaire', $depense->beneficiaire) }}" required>
                    @error('beneficiaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant" class="form-label">Montant <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant', $depense->montant) }}" required>
                    @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="mode_paiement_id" class="form-label">Mode de Paiement <span class="text-danger">*</span></label>
                    <select name="mode_paiement_id" id="mode_paiement_id" class="form-select @error('mode_paiement_id') is-invalid @enderror" required>
                        @foreach($modePaiements as $mode)
                            <option value="{{ $mode->id }}" {{ old('mode_paiement_id', $depense->mode_paiement_id) == $mode->id ? 'selected' : '' }}>
                                {{ $mode->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('mode_paiement_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="user_id" class="form-label">Agent <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $depense->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_depense" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date_depense" id="date_depense" class="form-control @error('date_depense') is-invalid @enderror" value="{{ old('date_depense', $depense->date_depense ? $depense->date_depense->format('Y-m-d') : '') }}" required>
                    @error('date_depense')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $depense->statut ? '1' : '0') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $depense->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
