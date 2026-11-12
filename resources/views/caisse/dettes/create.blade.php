@extends('layout')

@section('title', 'Déclarer une Dette')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Déclarer une Créance / Dette Patient</h1>
            <a href="{{ route('dettes.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('dettes.store') }}" method="POST" class="card card-body shadow-sm">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="ticket_id" class="form-label">Ticket Originel <span class="text-danger">*</span></label>
                    <select name="ticket_id" id="ticket_id" class="form-select @error('ticket_id') is-invalid @enderror" required>
                        <option value="">-- Choisir un ticket --</option>
                        @foreach($tickets as $ticket)
                            <option value="{{ $ticket->id }}" {{ old('ticket_id') == $ticket->id ? 'selected' : '' }}>
                                {{ $ticket->reference }} (Patient: {{ $ticket->patient->nom ?? 'Inconnu' }})
                            </option>
                        @endforeach
                    </select>
                    @error('ticket_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="patient_id" class="form-label">Patient Débiteurs <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">-- Choisir le patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
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
                    <label for="montant_initial" class="form-label">Montant Initial Dette (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_initial" id="montant_initial" class="form-control @error('montant_initial') is-invalid @enderror" value="{{ old('montant_initial') }}" required>
                    @error('montant_initial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_paye" class="form-label">Montant Déjà Réglé (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="montant_paye" id="montant_paye" class="form-control @error('montant_paye') is-invalid @enderror" value="{{ old('montant_paye', 0) }}">
                    @error('montant_paye')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="reste_a_payer" class="form-label">Reste à Payer (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="reste_a_payer" id="reste_a_payer" class="form-control @error('reste_a_payer') is-invalid @enderror" value="{{ old('reste_a_payer') }}" required>
                    @error('reste_a_payer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="date_creation" class="form-label">Date Création <span class="text-danger">*</span></label>
                    <input type="date" name="date_creation" id="date_creation" class="form-control @error('date_creation') is-invalid @enderror" value="{{ old('date_creation', date('Y-m-d')) }}" required>
                    @error('date_creation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="date_reglement" class="form-label">Date limite / Règlement</label>
                    <input type="date" name="date_reglement" id="date_reglement" class="form-control @error('date_reglement') is-invalid @enderror" value="{{ old('date_reglement') }}">
                    @error('date_reglement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', 'en_cours') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Motif / Observations</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Enregistrer la dette</button>
            </div>
        </form>
    </section>
@endsection
