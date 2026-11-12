@extends('layout')

@section('title', 'Modifier la Dette')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier la Dette #{{ $dette->id }}</h1>
            <a href="{{ route('dettes.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('dettes.update', $dette) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="ticket_id" class="form-label">Ticket <span class="text-danger">*</span></label>
                    <select name="ticket_id" id="ticket_id" class="form-select @error('ticket_id') is-invalid @enderror" required>
                        @foreach($tickets as $ticket)
                            <option value="{{ $ticket->id }}" {{ old('ticket_id', $dette->ticket_id) == $ticket->id ? 'selected' : '' }}>
                                {{ $ticket->reference }}
                            </option>
                        @endforeach
                    </select>
                    @error('ticket_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $dette->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="user_id" class="form-label">Agent <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $dette->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_initial" class="form-label">Montant Initial <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_initial" id="montant_initial" class="form-control @error('montant_initial') is-invalid @enderror" value="{{ old('montant_initial', $dette->montant_initial) }}" required>
                    @error('montant_initial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_paye" class="form-label">Montant Payé</label>
                    <input type="number" step="0.01" min="0" name="montant_paye" id="montant_paye" class="form-control @error('montant_paye') is-invalid @enderror" value="{{ old('montant_paye', $dette->montant_paye) }}">
                    @error('montant_paye')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="reste_a_payer" class="form-label">Reste à Payer <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="reste_a_payer" id="reste_a_payer" class="form-control @error('reste_a_payer') is-invalid @enderror" value="{{ old('reste_a_payer', $dette->reste_a_payer) }}" required>
                    @error('reste_a_payer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="date_creation" class="form-label">Date Création <span class="text-danger">*</span></label>
                    <input type="date" name="date_creation" id="date_creation" class="form-control @error('date_creation') is-invalid @enderror" value="{{ old('date_creation', $dette->date_creation ? $dette->date_creation->format('Y-m-d') : '') }}" required>
                    @error('date_creation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="date_reglement" class="form-label">Date Règlement</label>
                    <input type="date" name="date_reglement" id="date_reglement" class="form-control @error('date_reglement') is-invalid @enderror" value="{{ old('date_reglement', $dette->date_reglement ? $dette->date_reglement->format('Y-m-d') : '') }}">
                    @error('date_reglement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $dette->statut) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Observations</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $dette->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
