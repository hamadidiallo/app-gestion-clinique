@extends('layout')

@section('title', 'Modifier le Mouvement de Caisse')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier le Mouvement : {{ $mouvementCaisse->reference }}</h1>
            <a href="{{ route('mouvementcaisses.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('mouvementcaisses.update', $mouvementCaisse) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $mouvementCaisse->reference) }}" required>
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="caisse_id" class="form-label">Caisse <span class="text-danger">*</span></label>
                    <select name="caisse_id" id="caisse_id" class="form-select @error('caisse_id') is-invalid @enderror" required>
                        @foreach($caisses as $caisse)
                            <option value="{{ $caisse->id }}" {{ old('caisse_id', $mouvementCaisse->caisse_id) == $caisse->id ? 'selected' : '' }}>
                                Session #{{ $caisse->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('caisse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="user_id" class="form-label">Agent <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $mouvementCaisse->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $mouvementCaisse->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="origine" class="form-label">Origine <span class="text-danger">*</span></label>
                    <input type="text" name="origine" id="origine" class="form-control @error('origine') is-invalid @enderror" value="{{ old('origine', $mouvementCaisse->origine) }}" required>
                    @error('origine')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant" class="form-label">Montant <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant', $mouvementCaisse->montant) }}" required>
                    @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_mouvement" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="date_mouvement" id="date_mouvement" class="form-control @error('date_mouvement') is-invalid @enderror" value="{{ old('date_mouvement', $mouvementCaisse->date_mouvement ? $mouvementCaisse->date_mouvement->format('Y-m-d\TH:i') : '') }}" required>
                    @error('date_mouvement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $mouvementCaisse->statut ? '1' : '0') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $mouvementCaisse->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
