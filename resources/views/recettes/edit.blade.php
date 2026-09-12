@extends('layout')

@section('title', 'Modifier la Recette')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier la Recette : {{ $recette->reference }}</h1>
            <a href="{{ route('recettes.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('recettes.update', $recette) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $recette->reference) }}" required>
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="ticket_id" class="form-label">Ticket</label>
                    <select name="ticket_id" id="ticket_id" class="form-select @error('ticket_id') is-invalid @enderror">
                        <option value="">-- Aucun --</option>
                        @foreach($tickets as $ticket)
                            <option value="{{ $ticket->id }}" {{ old('ticket_id', $recette->ticket_id) == $ticket->id ? 'selected' : '' }}>
                                {{ $ticket->reference }}
                            </option>
                        @endforeach
                    </select>
                    @error('ticket_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="paiement_id" class="form-label">Paiement</label>
                    <select name="paiement_id" id="paiement_id" class="form-select @error('paiement_id') is-invalid @enderror">
                        <option value="">-- Aucun --</option>
                        @foreach($paiements as $paiement)
                            <option value="{{ $paiement->id }}" {{ old('paiement_id', $recette->paiement_id) == $paiement->id ? 'selected' : '' }}>
                                {{ $paiement->reference }}
                            </option>
                        @endforeach
                    </select>
                    @error('paiement_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant" class="form-label">Montant <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant" id="montant" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant', $recette->montant) }}" required>
                    @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="mode_paiement_id" class="form-label">Mode d'Encaissement</label>
                    <select name="mode_paiement_id" id="mode_paiement_id" class="form-select @error('mode_paiement_id') is-invalid @enderror">
                        <option value="">-- Choisir --</option>
                        @foreach($modePaiements as $mode)
                            <option value="{{ $mode->id }}" {{ old('mode_paiement_id', $recette->mode_paiement_id) == $mode->id ? 'selected' : '' }}>
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
                            <option value="{{ $user->id }}" {{ old('user_id', $recette->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_recette" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date_recette" id="date_recette" class="form-control @error('date_recette') is-invalid @enderror" value="{{ old('date_recette', $recette->date_recette ? $recette->date_recette->format('Y-m-d') : '') }}" required>
                    @error('date_recette')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $recette->statut ? '1' : '0') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $recette->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
