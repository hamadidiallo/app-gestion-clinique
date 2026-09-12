@extends('layout')

@section('title', 'Modifier la Ligne de Ticket')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier la Ligne #{{ $ticketdetail->id }}</h1>
            <a href="{{ route('ticketdetails.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('ticketdetails.update', $ticketdetail) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="ticket_id" class="form-label">Ticket Parent <span class="text-danger">*</span></label>
                    <select name="ticket_id" id="ticket_id" class="form-select @error('ticket_id') is-invalid @enderror" required>
                        @foreach($tickets as $ticket)
                            <option value="{{ $ticket->id }}" {{ old('ticket_id', $ticketdetail->ticket_id) == $ticket->id ? 'selected' : '' }}>
                                {{ $ticket->reference }}
                            </option>
                        @endforeach
                    </select>
                    @error('ticket_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="prestation_id" class="form-label">Prestation Médicale <span class="text-danger">*</span></label>
                    <select name="prestation_id" id="prestation_id" class="form-select @error('prestation_id') is-invalid @enderror" required>
                        @foreach($prestations as $prestation)
                            <option value="{{ $prestation->id }}" {{ old('prestation_id', $ticketdetail->prestation_id) == $prestation->id ? 'selected' : '' }}>
                                {{ $prestation->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('prestation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="quantite" id="quantite" class="form-control @error('quantite') is-invalid @enderror" value="{{ old('quantite', $ticketdetail->quantite) }}" required>
                    @error('quantite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="prix_unitaire" class="form-label">Prix Unitaire <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="prix_unitaire" id="prix_unitaire" class="form-control @error('prix_unitaire') is-invalid @enderror" value="{{ old('prix_unitaire', $ticketdetail->prix_unitaire) }}" required>
                    @error('prix_unitaire')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_total" class="form-label">Montant Total <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_total" id="montant_total" class="form-control @error('montant_total') is-invalid @enderror" value="{{ old('montant_total', $ticketdetail->montant_total) }}" required>
                    @error('montant_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_assurance" class="form-label">Part Assurance</label>
                    <input type="number" step="0.01" min="0" name="montant_assurance" id="montant_assurance" class="form-control @error('montant_assurance') is-invalid @enderror" value="{{ old('montant_assurance', $ticketdetail->montant_assurance) }}">
                    @error('montant_assurance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_patient" class="form-label">Part Patient <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_patient" id="montant_patient" class="form-control @error('montant_patient') is-invalid @enderror" value="{{ old('montant_patient', $ticketdetail->montant_patient) }}" required>
                    @error('montant_patient')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $ticketdetail->statut ? '1' : '0') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Observations</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $ticketdetail->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
