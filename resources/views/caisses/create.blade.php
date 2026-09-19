@extends('layout')

@section('title', 'Ouvrir une Session de Caisse - CLINGEST')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px;">
    {{-- Fil d'Ariane & En-tête --}}
    <div class="mb-4">
        <div class="small text-muted mb-1 d-flex align-items-center gap-1">
            <a href="{{ route('caisses.index') }}" class="text-decoration-none text-muted">Sessions de Caisse</a>
            <span class="opacity-50">/</span>
            <span class="fw-semibold text-dark">Ouverture</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Ouvrir un Guichet / Session</h1>
                <p class="text-muted small mb-0">Initialisez le fond de caisse pour démarrer la prise en charge des encaissements.</p>
            </div>
            <a href="{{ route('caisses.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" class="lucide-sm"></i>
                <span>Annuler</span>
            </a>
        </div>
    </div>

    <form action="{{ route('caisses.store') }}" method="POST" class="card shadow-sm border-0">
        @csrf
        <input type="hidden" name="statut" value="ouverte">

        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #e3f3ee; color: #0f6b5f;">
                <i data-lucide="wallet" class="lucide-sm"></i>
            </div>
            <h5 class="card-title mb-0 fw-bold text-dark">Paramètres de la Session</h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                {{-- Caissier --}}
                <div class="col-md-6">
                    <label for="user_id" class="form-label fw-semibold text-dark">
                        Caissier(ère) Responsable <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="user" class="lucide-sm text-muted"></i></span>
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name ?? $user->nom }} ({{ $user->role->nom ?? 'Agent' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('user_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Date d'ouverture --}}
                <div class="col-md-6">
                    <label for="date_ouverture" class="form-label fw-semibold text-dark">
                        Date & Heure d'Ouverture <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i data-lucide="calendar" class="lucide-sm text-muted"></i></span>
                        <input type="datetime-local" name="date_ouverture" id="date_ouverture" class="form-control @error('date_ouverture') is-invalid @enderror" value="{{ old('date_ouverture', date('Y-m-d\TH:i')) }}" required>
                    </div>
                    @error('date_ouverture')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Fond Initial --}}
                <div class="col-12">
                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <label for="fonds_initial" class="form-label fw-bold text-dark mb-1">
                            Fond de Caisse Initial (Monnaie de départ) <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">Montant liquide présent dans le tiroir au début de la vacation.</p>
                        <div class="input-group input-group-lg">
                            <input type="number" step="100" min="0" name="fonds_initial" id="fonds_initial" class="form-control font-mono fw-bold @error('fonds_initial') is-invalid @enderror" value="{{ old('fonds_initial', 0) }}" placeholder="Ex: 25000" required>
                            <span class="input-group-text font-mono fw-bold bg-white text-muted">FCFA</span>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 font-mono" onclick="document.getElementById('fonds_initial').value=0">0 F</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 font-mono" onclick="document.getElementById('fonds_initial').value=10000">10 000 F</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 font-mono" onclick="document.getElementById('fonds_initial').value=25000">25 000 F</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 font-mono" onclick="document.getElementById('fonds_initial').value=50000">50 000 F</button>
                        </div>
                        @error('fonds_initial')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Observation / Poste --}}
                <div class="col-12">
                    <label for="observation" class="form-label fw-semibold text-dark">
                        Poste ou Remarques
                    </label>
                    <input type="text" name="observation" id="observation" class="form-control @error('observation') is-invalid @enderror" value="{{ old('observation') }}" placeholder="Ex: Guichet 1 - Consultations matin">
                    @error('observation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card-footer bg-light py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <i data-lucide="shield-alert" class="lucide-sm text-warning align-middle me-1"></i>
                Chaque encaissement enregistré sera automatiquement affecté à cette session.
            </span>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                <i data-lucide="check-circle" class="lucide-sm"></i>
                <span class="fw-semibold">Ouvrir la Caisse</span>
            </button>
        </div>
    </form>
</div>
@endsection
