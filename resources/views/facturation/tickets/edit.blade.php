@extends('layout')

@section('title', 'Modifier le Ticket')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier le Ticket : {{ $ticket->reference }}</h1>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="card card-body shadow-sm">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-3">
                    <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                    <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $ticket->reference) }}" required>
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $ticket->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="assurance_id" class="form-label">Assurance Partner</label>
                    <select name="assurance_id" id="assurance_id" class="form-select @error('assurance_id') is-invalid @enderror">
                        <option value="">-- Aucune (100% Patient) --</option>
                        @foreach($assurances as $assurance)
                            <option value="{{ $assurance->id }}" {{ old('assurance_id', $ticket->assurance_id) == $assurance->id ? 'selected' : '' }}>
                                {{ $assurance->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('assurance_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="user_id" class="form-label">Agent Créateur <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $ticket->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="service_id" class="form-label">Service Médical</label>
                    <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror">
                        <option value="">-- Aucun Service Sélectionné --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id', $ticket->service_id) == $service->id ? 'selected' : '' }}>
                                {{ $service->nom }} {{ $service->code ? '('.$service->code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="medecin_id" class="form-label">Médecin Traitant & Spécialité</label>
                    <select name="medecin_id" id="medecin_id" class="form-select @error('medecin_id') is-invalid @enderror">
                        <option value="">-- Aucun Médecin Sélectionné --</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ old('medecin_id', $ticket->medecin_id) == $medecin->id ? 'selected' : '' }}>
                                Dr {{ $medecin->nom }} {{ $medecin->prenom }} {{ $medecin->specialite ? ' (Spécialité : '.$medecin->specialite.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('medecin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_ticket" class="form-label">Date du Ticket <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="date_ticket" id="date_ticket" class="form-control @error('date_ticket') is-invalid @enderror" value="{{ old('date_ticket', $ticket->date_ticket ? $ticket->date_ticket->format('Y-m-d\TH:i') : '') }}" required>
                    @error('date_ticket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="date_expiration" class="form-label">Date d'Expiration</label>
                    <input type="datetime-local" name="date_expiration" id="date_expiration" class="form-control @error('date_expiration') is-invalid @enderror" value="{{ old('date_expiration', $ticket->date_expiration ? $ticket->date_expiration->format('Y-m-d\TH:i') : '') }}">
                    @error('date_expiration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_total" class="form-label">Montant Total <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_total" id="montant_total" class="form-control @error('montant_total') is-invalid @enderror" value="{{ old('montant_total', $ticket->montant_total) }}" required>
                    @error('montant_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_assurance" class="form-label">Part Assurance</label>
                    <input type="number" step="0.01" min="0" name="montant_assurance" id="montant_assurance" class="form-control @error('montant_assurance') is-invalid @enderror" value="{{ old('montant_assurance', $ticket->montant_assurance) }}">
                    @error('montant_assurance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_patient" class="form-label">Part Patient <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="montant_patient" id="montant_patient" class="form-control @error('montant_patient') is-invalid @enderror" value="{{ old('montant_patient', $ticket->montant_patient) }}" required>
                    @error('montant_patient')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="montant_paye" class="form-label">Montant Payé</label>
                    <input type="number" step="0.01" min="0" name="montant_paye" id="montant_paye" class="form-control @error('montant_paye') is-invalid @enderror" value="{{ old('montant_paye', $ticket->montant_paye) }}">
                    @error('montant_paye')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}" {{ old('statut', $ticket->statut) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label">Observations</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $ticket->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
@endsection
